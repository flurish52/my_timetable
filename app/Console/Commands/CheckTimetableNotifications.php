<?php

namespace App\Console\Commands;

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
        $this->info('Starting ...');
        $now = now();
        $currentDay = strtolower($now->format('l'));
        $nowMinutes = $now->hour * 60 + $now->minute;
        $currentHour = $now->hour;
        $currentMinute = $now->minute;

        // ── Morning greeting (6:00 AM) ────────────────────────────────────────
        if ($currentHour === 6 && $currentMinute === 0) {
            // For each user, count their lectures today and send a personalised morning push
            User::with(['deviceTokens'])->whereHas('deviceTokens')->chunk(100, function ($users) use ($currentDay) {
                foreach ($users as $user) {
                    $lectureCount = TimetableSlot::where('programme_id', $user->programme_id)
                        ->whereRaw('LOWER(day_of_week) = ?', [$currentDay])
                        ->count();

                    $this->sendPushToTokens(
                        $user->deviceTokens->pluck('token')->toArray(),
                        'Hi, Good morning 🌅',
                        "You have {$lectureCount} lecture" . ($lectureCount === 1 ? '' : 's') . ' today!'
                    );
                }
            });
        }

        $this->info('Going ...');
        // ── Evening greeting (7:00 PM) ────────────────────────────────────────
        if ($currentHour === 19 && $currentMinute === 0) {
            User::with(['deviceTokens'])->whereHas('deviceTokens')->chunk(100, function ($users) {
                $tokens = $users->flatMap(fn($u) => $u->deviceTokens->pluck('token'))->toArray();
                $this->sendPushToTokens($tokens, 'Hi, Good evening 😊', 'How was your day?');
            });
        }

        // ── Per-lecture notifications (10 min, 5 min, now) ───────────────────
        $targetDiffs = [10, 0];

        $this->info('Still working close to diff, targetDiff');
        foreach ($targetDiffs as $diff) {
            // Calculate the start_time we're looking for
            $targetMinutes = $nowMinutes + $diff;
            $targetHour    = intdiv($targetMinutes, 60);
            $targetMin     = $targetMinutes % 60;
            $targetTime    = sprintf('%02d:%02d:00', $targetHour, $targetMin);

            // Find all timetable entries starting at that exact time today
            $entries = TimetableSlot::with(['course'])
                ->whereRaw('LOWER(day_of_week) = ?', [$currentDay])
                ->where('start_time', $targetTime)
                ->get();
            $this->info("diff={$diff} | targetTime={$targetTime} | entries=" . $entries->count());
            foreach ($entries as $entry) {
                // Find users whose programme matches this timetable entry
                $users = User::where('programme_id', $entry->programme_id)
                    ->whereHas('deviceTokens')
                    ->with('deviceTokens')
                    ->get();

                if ($users->isEmpty()) {
                    continue;
                }

                $tokens = $users->flatMap(fn($u) => $u->deviceTokens->pluck('token'))->toArray();

                $courseCode  = $entry->course?->code  ?? 'Class';
                $courseTitle = $entry->course?->title ?? 'Your class';
                $venue       = $entry->venue          ?? 'TBA';

                $title = match ($diff) {
                    10 => "⏰ {$courseCode} class in 10 minutes",
                    0  => "🏫 {$courseCode} class is starting now",
                };

                $body = "{$courseTitle} at {$venue}";

                $this->sendPushToTokens($tokens, $title, $body);
                $this->info('sent already');
            }
        }
        $this->info('end line');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function sendPushToTokens(array $tokens, string $title, string $body): void
    {
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
            Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token'        => $token,
                        'notification' => ['title' => $title, 'body' => $body],
                        'data'         => ['title' => $title, 'body' => $body],
                    ],
                ]);
        }
    }

    private function getCredentials(): array
    {
        return json_decode(
            file_get_contents(storage_path('app/mytimetable-9beae-firebase-adminsdk-fbsvc-7b660a4108.json')),
            true
        );
    }

    private function getAccessToken(array $credentials): ?string
    {
        $base64Url = fn($data) => rtrim(strtr(base64_encode($data), '+/', '-_'), '=');

        $now = time();

        $header  = $base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = $base64Url(json_encode([
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        openssl_sign($header . '.' . $payload, $signature, $credentials['private_key'], 'sha256');

        $jwt = $header . '.' . $payload . '.' . $base64Url($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        return $response['access_token'] ?? null;
    }
}
