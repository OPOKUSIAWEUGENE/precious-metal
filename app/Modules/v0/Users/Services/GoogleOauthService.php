<?php

namespace App\Modules\v0\Users\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleOauthService
{
    protected string $tokenUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $passengerRedirectUri;
    protected string $userInfoUrl;

    public function __construct()
    {
        $this->tokenUrl = config('services.google.oauth.token_url');
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
        $this->redirectUri = config('services.google.oauth.redirect_uri');
        $this->passengerRedirectUri = config('services.google.oauth.passenger_redirect_uri');
        $this->userInfoUrl = config('services.google.oauth.userinfo_url');
    }

    /**
     * Get redirect URI based on context (general or passenger).
     */
    public function getRedirectUri(bool $isPassenger = false): string
    {
        return $isPassenger ? $this->passengerRedirectUri : $this->redirectUri;
    }

    /**
     * Exchange authorization code for tokens and profile.
     */
    public function exchangeCodeForProfile(string $code, bool $isPassenger = false): array
    {
        $tokenResponse = $this->exchangeForTokens($code, $isPassenger);
        
        if (empty($tokenResponse)) {
            return [];
        }

        $profile = $this->fetchProfile($tokenResponse);
        $profile['token_response'] = $tokenResponse;

        return $profile;
    }

    /**
     * Exchange authorization code for access and ID tokens.
     */
    protected function exchangeForTokens(string $code, bool $isPassenger = false): array
    {
        $redirectUri = $this->getRedirectUri($isPassenger);
        
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

            Log::error('GoogleOauthService::exchangeForTokens - Failed to exchange code', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to exchange code: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('GoogleOauthService::exchangeForTokens - ' . $e->getMessage());
            throw new \Exception('Failed to exchange code: ' . $e->getMessage());
        }
    }

    /**
     * Fetch user profile from Google using access token.
     */
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
                
                // Decode ID token if present
                if (isset($tokens['id_token'])) {
                    $profile['id_token_claims'] = $this->decodeIdToken($tokens['id_token']);
                }

                return $profile;
            }

            Log::error('GoogleOauthService::fetchProfile - Failed to fetch profile', [
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('GoogleOauthService::fetchProfile - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Decode Google ID token and return claims.
     */
    public function decodeIdToken(?string $idToken): array
    {
        if (empty($idToken)) {
            return [];
        }

        try {
            // Split the JWT token
            $parts = explode('.', $idToken);
            
            if (count($parts) < 2) {
                return [];
            }

            // Decode the payload (second part)
            $payload = base64_decode($this->urlSafeBase64Decode($parts[1]));

            return json_decode($payload, true) ?? [];
        } catch (\Exception $e) {
            Log::error('GoogleOauthService::decodeIdToken - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Decode URL-safe base64 string.
     */
    protected function urlSafeBase64Decode(string $data): string
    {
        // Add padding if needed
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }

    /**
     * Validate and decode ID token.
     */
    public function validateIdToken(string $idToken): ?array
    {
        try {
            $claims = $this->decodeIdToken($idToken);
            
            if (empty($claims)) {
                return null;
            }

            // Check expiration
            if (isset($claims['exp']) && $claims['exp'] < time()) {
                return null;
            }

            // Check issuer
            if (isset($claims['iss']) && $claims['iss'] !== 'https://accounts.google.com' && $claims['iss'] !== 'accounts.google.com') {
                return null;
            }

            // Check audience
            if (isset($claims['aud']) && $claims['aud'] !== $this->clientId) {
                return null;
            }

            return $claims;
        } catch (\Exception $e) {
            Log::error('GoogleOauthService::validateIdToken - ' . $e->getMessage());
            return null;
        }
    }
}