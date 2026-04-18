<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckTimetableNotifications extends Command
{
    protected $signature = 'timetable:check';
    protected $description = 'Check timetable and send notifications';

    public function handle()
    {

        $filePath = public_path('data/timetable.json');

        if (!file_exists($filePath)) {
            $this->error('Timetable file not found');
            return;
        }

        $data = json_decode(file_get_contents($filePath), true);

        if (!$data) {
            $this->error('Invalid JSON');
            return;
        }

        $now = now();

        $currentDay = strtolower($now->format('l'));
        $nowMinutes = $now->hour * 60 + $now->minute;

        $todayClasses = $data['timetable'][$currentDay] ?? [];

        $currentHour = now()->hour;
        $currentMinute = now()->minute;

// count only real classes
        $lectureCount = collect($todayClasses)
            ->filter(fn($c) => !empty($c['course']))
            ->count();
        // MORNING: 6:00 AM
        if ($currentHour == 06 && $currentMinute == 00) {
            $title = "Hi, Good morning🌅";
            $body = "You have {$lectureCount} lectures today!";

            $this->sendGeneralPush($title, $body);
        }

        // EVENING: 6:00 PM
        if ($currentHour == 19 && $currentMinute == 00) {
            $title = "Hi, Good evening 😊";
            $body = "How was your day?";

            $this->sendGeneralPush($title, $body);
        }

        foreach ($todayClasses as $class) {

            if (empty($class['course'])) {
                continue;
            }

            [$h, $m] = explode(':', $class['start']);
            $classMinutes = ($h * 60) + $m;

            $diff = $classMinutes - $nowMinutes;

            if (!in_array($diff, [10, 5, 0])) {
                continue;
            }

            $this->sendPush($class, $diff);
        }
    }

    private function sendPush($class, $diff)
    {
        $credentials = json_decode(
            file_get_contents(storage_path('app/mytimetable-9beae-firebase-adminsdk-fbsvc-7b660a4108.json')),
            true
        );

        $projectId = $credentials['project_id'];

        $accessToken = $this->getAccessToken($credentials);
        if (!$accessToken) {
            return;
        }

        $title = match ($diff) {
            30 => '⏰' . $class['code'] . ' class in 30 minutes',
            10 => '⏰' . $class['code'] . ' class in 10 minutes',
            5 => '🔔' . $class['code'] . ' class in 5 minutes',
            0 => '🏫' . $class['code'] . ' class is starting now',
        };

        $body = $class['course'] . ' (' . $class['code'] . ') at ' . $class['venue'];
        $tokens = json_decode(
            file_get_contents(storage_path('app/fcm_tokens.json')),
            true
        );
            foreach ($tokens as $token) {
                Http::withToken($accessToken)
                    ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                        ]
                    ]);
            }
        }

    private function getAccessToken($credentials)
    {
        $base64Url = fn($data) => rtrim(strtr(base64_encode($data), '+/', '-_'), '=');

        $now = time();

        $header = $base64Url(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));

        $payload = $base64Url(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        openssl_sign(
            $header . "." . $payload,
            $signature,
            $credentials['private_key'],
            'sha256'
        );

        $jwt = $header . "." . $payload . "." . $base64Url($signature);

        $response = Http::asForm()->post(
            'https://oauth2.googleapis.com/token',
            [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]
        );

        return $response['access_token']  ?? null;
    }



    private function sendGeneralPush($title, $body)
    {
        $credentials = json_decode(
            file_get_contents(storage_path('app/mytimetable-9beae-firebase-adminsdk-fbsvc-7b660a4108.json')),
            true
        );

        $projectId = $credentials['project_id'];

        $accessToken = $this->getAccessToken($credentials);
        if (!$accessToken) {
            return;
        }

        $tokens = json_decode(
            file_get_contents(storage_path('app/fcm_tokens.json')),
            true
        );
        foreach ($tokens as $token) {
            Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ]
                ]);
        }
    }
}
