<?php

namespace App\Modules\v0\Auth\Services\Customer;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerGoogleOauthService
{
    protected string $tokenUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $customerRedirectUri;
    protected string $userInfoUrl;

    public function __construct()
    {
        $this->tokenUrl = config('services.google.oauth.token_url');
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
        $this->redirectUri = config('services.google.oauth.redirect_uri');
        $this->customerRedirectUri = config('services.google.oauth.customer_redirect_uri')
            ?? config('services.google.oauth.passenger_redirect_uri');
        $this->userInfoUrl = config('services.google.oauth.userinfo_url');
    }

    public function getRedirectUri(bool $isCustomer = false): string
    {
        return $isCustomer ? $this->customerRedirectUri : $this->redirectUri;
    }

    public function exchangeCodeForProfile(string $code, bool $isCustomer = false): array
    {
        $tokenResponse = $this->exchangeForTokens($code, $isCustomer);

        if (empty($tokenResponse)) {
            return [];
        }

        $profile = $this->fetchProfile($tokenResponse);
        $profile['token_response'] = $tokenResponse;

        return $profile;
    }

    protected function exchangeForTokens(string $code, bool $isCustomer = false): array
    {
        $redirectUri = $this->getRedirectUri($isCustomer);

        try {
            $response = Http::asForm()->post($this->tokenUrl, [
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CustomerGoogleOauthService::exchangeForTokens - Failed to exchange code', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to exchange code: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('CustomerGoogleOauthService::exchangeForTokens - ' . $e->getMessage());
            throw new \Exception('Failed to exchange code: ' . $e->getMessage());
        }
    }

    protected function fetchProfile(array $tokens): array
    {
        $accessToken = $tokens['access_token'] ?? null;

        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($this->userInfoUrl);

            if ($response->successful()) {
                $profile = $response->json();
                if (isset($tokens['id_token'])) {
                    $profile['id_token_claims'] = $this->decodeIdToken($tokens['id_token']);
                }

                return $profile;
            }

            Log::error('CustomerGoogleOauthService::fetchProfile - Failed to fetch profile', [
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('CustomerGoogleOauthService::fetchProfile - ' . $e->getMessage());
            return [];
        }
    }

    public function decodeIdToken(?string $idToken): array
    {
        if (empty($idToken)) {
            return [];
        }

        try {
            $parts = explode('.', $idToken);
            if (count($parts) < 2) {
                return [];
            }

            $payload = base64_decode($this->urlSafeBase64Decode($parts[1]));
            return json_decode($payload, true) ?? [];
        } catch (\Exception $e) {
            Log::error('CustomerGoogleOauthService::decodeIdToken - ' . $e->getMessage());
            return [];
        }
    }

    protected function urlSafeBase64Decode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }

    public function validateIdToken(string $idToken): ?array
    {
        try {
            $claims = $this->decodeIdToken($idToken);

            if (empty($claims)) {
                return null;
            }

            if (isset($claims['exp']) && $claims['exp'] < time()) {
                return null;
            }

            if (isset($claims['iss']) && $claims['iss'] !== 'https://accounts.google.com' && $claims['iss'] !== 'accounts.google.com') {
                return null;
            }

            if (isset($claims['aud']) && $claims['aud'] !== $this->clientId) {
                return null;
            }

            return $claims;
        } catch (\Exception $e) {
            Log::error('CustomerGoogleOauthService::validateIdToken - ' . $e->getMessage());
            return null;
        }
    }
}
