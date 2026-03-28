<?php

namespace App\Modules\v0\Users\Services;

class GoogleAuthUrlService
{
    protected string $authUrl;
    protected string $clientId;
    protected string $redirectUri;
    protected string $passengerRedirectUri;
    protected string $scope;
    protected string $responseType;
    protected string $accessType;
    protected string $prompt;
    protected bool $includeGrantedScopes;

    public function __construct()
    {
        $this->authUrl = config('services.google.oauth.auth_url');
        $this->clientId = config('services.google.client_id');
        $this->redirectUri = config('services.google.oauth.redirect_uri');
        $this->passengerRedirectUri = config('services.google.oauth.passenger_redirect_uri');
        $this->scope = config('services.google.oauth.scope', 'openid email profile');
        $this->responseType = config('services.google.oauth.response_type', 'code');
        $this->accessType = config('services.google.oauth.access_type', 'offline');
        $this->prompt = config('services.google.oauth.prompt', 'consent');
        $this->includeGrantedScopes = filter_var(config('services.google.oauth.include_granted_scopes', true), FILTER_VALIDATE_BOOLEAN);
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
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->passengerRedirectUri,
            'response_type' => $this->responseType,
            'scope' => $this->scope,
            'access_type' => $this->accessType,
            'prompt' => $this->prompt,
            'include_granted_scopes' => $this->includeGrantedScopes ? 'true' : 'false',
        ];

        return $this->authUrl . '?' . http_build_query($params);
    }
}