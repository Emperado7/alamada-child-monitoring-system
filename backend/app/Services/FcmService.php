<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends push notifications via Firebase Cloud Messaging (FCM) HTTP v1 API.
 *
 * Requires the following in .env:
 *   FIREBASE_PROJECT_ID=your-project-id
 *   FIREBASE_CREDENTIALS=/full/path/to/service-account.json
 *
 * The service account JSON is downloadable from:
 *   Firebase Console → Project Settings → Service Accounts → Generate New Private Key
 *
 * This class handles OAuth2 token generation from the service account's
 * private key and caches the access token until it expires (~1 hour).
 */
class FcmService
{
    private ?array $credentials = null;

    /**
     * Send a push notification to one or more device tokens.
     *
     * @param array  $tokens   FCM registration tokens
     * @param string $title    Notification title
     * @param string $body      Notification body
     * @param array  $data     Optional data payload
     * @return int  Number of messages sent
     */
    public function send(array $tokens, string $title, string $body, array $data = []): int
    {
        if (empty($tokens) || !$this->isConfigured()) {
            return 0;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return 0;
        }

        $projectId = config('firebase.project_id') ?: env('FIREBASE_PROJECT_ID');
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $sent = 0;

        foreach ($tokens as $token) {
            if (empty($token)) continue;

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => array_map('strval', $data),
                    'android' => [
                        'priority' => 'high',
                    ],
                ],
            ];

            try {
                $response = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);

                if ($response->successful()) {
                    $sent++;
                } else {
                    Log::warning('FCM send failed', [
                        'token' => substr($token, 0, 20) . '...',
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('FCM exception: ' . $e->getMessage());
            }
        }

        return $sent;
    }

    /**
     * Check if FCM is properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty(env('FIREBASE_PROJECT_ID')) && !empty(env('FIREBASE_CREDENTIALS'));
    }

    /**
     * Get the OAuth2 access token, generating and caching it if needed.
     */
    private function getAccessToken(): ?string
    {
        $cached = Cache::get('fcm_access_token');
        if ($cached) {
            return $cached;
        }

        $creds = $this->getCredentials();
        if (!$creds) {
            return null;
        }

        // Build a signed JWT for Google OAuth2
        $now = time();
        $claim = [
            'iss'   => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        ];

        $jwt = $this->createJwt($claim, $creds['private_key']);
        if (!$jwt) {
            return null;
        }

        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type'    => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'     => $jwt,
            ]);

            if ($response->successful()) {
                $token = $response->json('access_token');
                $expiresIn = $response->json('expires_in', 3600);
                // Cache for 5 minutes less than expiry as a safety margin
                Cache::put('fcm_access_token', $token, now()->addSeconds($expiresIn - 300));
                return $token;
            }
        } catch (\Exception $e) {
            Log::error('FCM OAuth2 token error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Load Firebase service account credentials from the JSON file.
     */
    private function getCredentials(): ?array
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $path = env('FIREBASE_CREDENTIALS');
        if (!$path || !file_exists($path)) {
            Log::warning('FCM: credentials file not found at ' . $path);
            return null;
        }

        $json = file_get_contents($path);
        $creds = json_decode($json, true);
        if (!isset($creds['private_key'], $creds['client_email'])) {
            Log::error('FCM: invalid service account JSON');
            return null;
        }

        $this->credentials = $creds;
        return $creds;
    }

    /**
     * Create a JWT signed with the service account's private key.
     */
    private function createJwt(array $claim, string $privateKeyPem): ?string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];

        $segments = [];
        $segments[] = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $segments[] = $this->base64UrlEncode(json_encode($claim, JSON_UNESCAPED_SLASHES));

        $signingInput = implode('.', $segments);

        $signature = '';
        $result = openssl_sign(
            $signingInput,
            $signature,
            $privateKeyPem,
            'sha256WithRSAEncryption'
        );

        if (!$result) {
            Log::error('FCM: JWT signing failed');
            return null;
        }

        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
