<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FcmPushService
{
    /**
     * IMPORTANT: data-only payload. Do NOT add a top-level "notification"
     * key here — sending both "notification" and "data" causes
     * Android/iOS to display the system-tray banner AND your foreground/
     * background handler to display it again — the double-notification
     * bug this app hit before.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $extraData = []): void
    {
        if (empty($tokens)) {
            return;
        }

        $credentials = $this->getCredentials();
        $accessToken = $this->getAccessToken($credentials);

        if (! $accessToken) {
            report(new \RuntimeException('Could not obtain Firebase access token.'));
            return;
        }

        $projectId = $credentials['project_id'];

        foreach ($tokens as $token) {
            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $token,
                        'data' => array_merge([
                            'title' => $title,
                            'body' => $body,
                        ], $extraData),
                    ],
                ]);

            if ($response->failed()) {
                report(new \RuntimeException("FCM rejected token {$token}: " . $response->body()));
            }
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
        $base64Url = fn ($data) => rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        $now = time();

        $header = $base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = $base64Url(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        openssl_sign($header . '.' . $payload, $signature, $credentials['private_key'], 'sha256');
        $jwt = $header . '.' . $payload . '.' . $base64Url($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response['access_token'] ?? null;
    }
}
