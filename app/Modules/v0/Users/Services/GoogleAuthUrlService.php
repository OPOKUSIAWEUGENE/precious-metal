<?php

namespace App\Modules\v0\Users\Services;

use Illuminate\Support\Facades\Cache;

class GoogleAuthUrlService
{
    protected string $authUrl;
    protected string $clientId;
    protected string $redirectUri;
    protected string $scope;
    protected string $responseType;
    protected string $accessType;
    protected string $prompt;
    protected bool $includeGrantedScopes;

    public function __construct()
    {
        $this->authUrl = config('services.google.oauth.auth_url', 'https://accounts.google.com/o/oauth2/v2/auth');
        $this->clientId = config('services.google.client_id', config('services.google.client_id', ''));
        $this->redirectUri = config('services.google.oauth.redirect_uri', config('app.url') . '/api/v0/auth/google/callback');
        $this->scope = config('services.google.oauth.scope', 'openid email profile');
        $this->responseType = config('services.google.oauth.response_type', 'code');
        $this->accessType = config('services.google.oauth.access_type', 'offline');
        $this->prompt = config('services.google.oauth.prompt', 'consent');
        $this->includeGrantedScopes = config('services.google.oauth.include_granted_scopes', true);
    }

    /**
     * Build the Google authorization URL.
     */
    public function buildAuthorizationUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => $this->responseType,
            'scope' => $this->scope,
            'access_type' => $this->accessType,
            'prompt' => $this->prompt,
            'include_granted_scopes' => $this->includeGrantedScopes ? 'true' : 'false',
        ];

        return $this->authUrl . '?' . http_build_query($params);
    }

    /**
     * Get the Google OAuth URL for passenger auth.
     */
    public function getPassengerAuthUrl(): string
    {
        // Override redirect URI for passenger
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => config('services.google.oauth.passenger_redirect_uri', config('app.url') . '/api/v0/passenger/auth/google/callback'),
            'response_type' => $this->responseType,
            'scope' => $this->scope,
            'access_type' => $this->accessType,
            'prompt' => $this->prompt,
            'include_granted_scopes' => $this->includeGrantedScopes ? 'true' : 'false',
        ];

        return $this->authUrl . '?' . http_build_query($params);
    }
}