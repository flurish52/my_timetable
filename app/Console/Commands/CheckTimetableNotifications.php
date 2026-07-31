<?php

namespace App\Console\Commands;

use App\Models\CourseOffering;
use App\Models\ProgrammeLevelSemester;
use App\Models\StudentElective;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckTimetableNotifications extends Command
{
    protected $signature = 'timetable:check';

    protected $description = 'Check timetable and send notifications';

    public function handle()
    {
        $now = now();

        $currentDay = strtolower($now->format('l'));

        $nowMinutes = ($now->hour * 60) + $now->minute;

        $currentHour = $now->hour;

        $currentMinute = $now->minute;

        $userCourseMap = [];

// Morning greeting
        if ($currentHour === 6 && $currentMinute === 0) {

            User::with('deviceTokens')
                ->whereHas('deviceTokens')
                ->chunk(100, function ($users) use (
                    $currentDay,
                    &$userCourseMap
                ) {

                    foreach ($users as $user) {

                        if (!isset($userCourseMap[$user->id])) {
                            $userCourseMap[$user->id] = $this->getUserCourseIds($user);
                        }

                        $courseIds = $userCourseMap[$user->id];

                        $lectureCount = TimetableSlot::whereIn('course_id', $courseIds)
                            ->whereRaw('LOWER(day_of_week) = ?', [$currentDay])
                            ->count();

                        $name = $user->username ?: 'there';

                        $title = 'Hi, Good morning ' . $name . ' 🌅';

                        $body = $lectureCount > 0
                            ? "You have {$lectureCount} lecture" . ($lectureCount === 1 ? '' : 's') . ' today!'
                            : 'You have no lectures today. Plan your day well.';

                        $this->sendPushToTokens(
                            $user->deviceTokens->pluck('token')->toArray(),
                            $title,
                            $body
                        );
                    }
                });
        }

// Evening greeting
        if ($currentHour === 19 && $currentMinute === 0) {

            User::with('deviceTokens')
                ->whereHas('deviceTokens')
                ->chunk(100, function ($users) {

                    $tokens = $users
                        ->flatMap(fn ($u) => $u->deviceTokens->pluck('token'))
                        ->toArray();
                    $this->sendPushToTokens(
                        $tokens,
                        'Hi, Good evening 😊',
                        'You are done for today. Take it easy and relax.'
                    );
                });
        }

        // Lecture notifications
        $targetDiffs = [10, 0];
        foreach ($targetDiffs as $diff) {
            $targetMinutes = $nowMinutes + $diff;

            $targetHour = intdiv($targetMinutes, 60);

            $targetMin = $targetMinutes % 60;

            $targetTime = sprintf('%02d:%02d:00', $targetHour, $targetMin);

            $entries = TimetableSlot::with('course')
                ->whereRaw('LOWER(day_of_week) = ?', [$currentDay])
                ->where('start_time', $targetTime)
                ->get();

            foreach ($entries as $entry) {

                $users = User::with('deviceTokens')
                    ->whereHas('deviceTokens')
                    ->get()
                    ->filter(function ($user) use (
                        $entry,
                        &$userCourseMap
                    ) {

                        if (!isset($userCourseMap[$user->id])) {
                            $userCourseMap[$user->id] = $this->getUserCourseIds($user);
                        }

                        return $userCourseMap[$user->id]
                            ->contains($entry->course_id);
                    });
                if ($users->isEmpty()) {
                    continue;
                }

                $tokens = $users
                    ->flatMap(fn ($u) => $u->deviceTokens->pluck('token'))
                    ->unique()
                    ->toArray();

                $courseCode = $entry->course?->code ?? 'Class';

                $courseTitle = $entry->course?->title ?? 'Your lecture';

                $venue = $entry->venue ?? 'TBA';

                $title = match ($diff) {
                    10 => "⏰ {$courseCode} lecture in 10 minutes",
                    0 => "🏫 {$courseCode} lecture is starting now",
                };

                $body = "{$courseTitle} at {$venue}";

                $this->sendPushToTokens($tokens, $title, $body);

            }
        }

    }

    private function sendPushToTokens(
        array $tokens,
        string $title,
        string $body
    ): void {

        if (empty($tokens)) {
            return;
        }

        $credentials = $this->getCredentials();

        $accessToken = $this->getAccessToken($credentials);

        if (!$accessToken) {

            $this->error('Could not obtain Firebase access token.');

            return;
        }

        $projectId = $credentials['project_id'];

        foreach ($tokens as $token) {

            // IMPORTANT: data-only payload. Do NOT add a top-level
            // "notification" key back in here — sending both
            // "notification" and "data" causes Android/iOS to display
            // the system-tray banner AND your foreground/background
            // handler to display it again, i.e. the double-notification
            // bug this app hit before.
            $response = Http::withToken($accessToken)
                ->post(
                    "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                    [
                        'message' => [
                            'token' => $token,
                            'data' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                        ],
                    ]
                );

            if ($response->failed()) {
                $this->error("FCM rejected token {$token}: " . $response->body());
            }
        }
    }

    private function getCredentials(): array
    {
        return json_decode(
            file_get_contents(
                storage_path(
                    'app/mytimetable-9beae-firebase-adminsdk-fbsvc-7b660a4108.json'
                )
            ),
            true
        );
    }

    private function getAccessToken(array $credentials): ?string
    {
        $base64Url = fn ($data) => rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );

        $now = time();

        $header = $base64Url(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $payload = $base64Url(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        openssl_sign(
            $header . '.' . $payload,
            $signature,
            $credentials['private_key'],
            'sha256'
        );

        $jwt = $header . '.'
            . $payload . '.'
            . $base64Url($signature);

        $response = Http::asForm()->post(
            'https://oauth2.googleapis.com/token',
            [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]
        );

        return $response['access_token'] ?? null;
    }

    /**
     * Returns the course IDs relevant to this user for their CURRENT
     * semester only. Mirrors the resolution logic used in
     * TimetableController@index: current semester comes from the
     * ProgrammeLevelSemester pivot (programme_id + level_id), not from
     * any semester_id column on the user.
     */
    private function getUserCourseIds(User $user)
    {
        $currentSemesterId = ProgrammeLevelSemester::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->value('semester_id');

        if (! $currentSemesterId) {
            return collect();
        }

        $coreCourseIds = CourseOffering::where('programme_id', $user->programme_id)
            ->where('level_id', $user->level_id)
            ->where('semester_id', $currentSemesterId)
            ->where(function ($query) {
                $query->where('type', 'core')
                    ->orWhere('is_general', true);
            })
            ->pluck('course_id');

        $electiveOfferingIds = StudentElective::where('student_id', $user->id)
            ->whereHas('courseOffering', function ($query) use ($currentSemesterId) {
                $query->where('semester_id', $currentSemesterId);
            })
            ->pluck('course_offering_id');

        $electiveCourseIds = CourseOffering::whereIn('id', $electiveOfferingIds)
            ->pluck('course_id');

        return $coreCourseIds
            ->merge($electiveCourseIds)
            ->unique();
    }
}
