<?php

namespace App\Providers;

use App\Models\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

class HelloAssoProvider extends AbstractProvider
{
    /**
     * The HelloAsso API base URL.
     */
    protected const API_BASE_URL = 'https://api.helloasso.com';

    /**
     * The HelloAsso Sandbox API base URL.
     */
    protected const SANDBOX_API_BASE_URL = 'https://api.helloasso-sandbox.com';

    /**
     * The scopes being requested.
     */
    protected $scopes = ['openid', 'profile', 'email', 'organizations'];

    /**
     * Use sandbox mode for testing.
     */
    public function sandbox(): self
    {
        $this->apiBaseUrl = self::SANDBOX_API_BASE_URL;

        return $this;
    }

    /**
     * Get the current API base URL.
     */
    protected function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl ?? self::API_BASE_URL;
    }

    /**
     * Get the authentication URL for the provider.
     */
    public function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            $this->getApiBaseUrl().'/oauth2/authorize',
            $state
        );
    }

    /**
     * Get the token URL for the provider.
     */
    public function getTokenUrl(): string
    {
        return $this->getApiBaseUrl().'/oauth2/token';
    }

    /**
     * Get the raw user for the given access token.
     */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get(
            $this->getApiBaseUrl().'/oauth2/userinfo',
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        $user = json_decode($response->getBody(), true);

        if (! is_array($user)) {
            throw new \Exception('Invalid user data received from HelloAsso');
        }

        // Récupérer les informations des organisations de l'utilisateur
        $user['organizations'] = $this->getUserOrganizations($token);

        return $user;
    }

    /**
     * Get the user's organizations from HelloAsso API.
     */
    protected function getUserOrganizations($token): array
    {
        try {
            $response = $this->getHttpClient()->get(
                $this->getApiBaseUrl().'/v5/users/me/organizations',
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json',
                    ],
                ]
            );

            $data = json_decode($response->getBody(), true);

            if (! is_array($data) || ! isset($data['data'])) {
                return [];
            }

            return $data['data'] ?? [];
        } catch (\Exception $e) {
            // Si l'API des organisations n'est pas disponible ou retourne une erreur,
            // on retourne un tableau vide pour ne pas casser l'authentification
            return [];
        }
    }

    /**
     * Map the raw user array to a Socialite User instance.
     */
    protected function mapUserToObject(array $user): SocialiteUser
    {
        return (new SocialiteUser)->setRaw($user)->map([
            'id' => $user['id'] ?? null,
            'nickname' => $user['nickname'] ?? null,
            'name' => $user['name'] ?? $user['given_name'].' '.$user['family_name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
            'organizations' => $user['organizations'] ?? [],
        ]);
    }

    /**
     * Get the token fields for the token request.
     */
    protected function getTokenFields($code): array
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * Get the code fields for the authorization request.
     */
    protected function getCodeFields($state = null): array
    {
        return [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
        ];
    }

    /**
     * Get the access token response for the given code.
     */
    public function getAccessTokenResponse($code): array
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => $this->getTokenFields($code),
        ]);

        $data = json_decode($response->getBody(), true);

        if (! is_array($data) || ! isset($data['access_token'])) {
            throw new \Exception('Invalid access token response from HelloAsso');
        }

        return $data;
    }

    /**
     * Get the default scopes for the provider.
     */
    protected function getDefaultScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Get additional configuration keys that should be loaded from config/services.php.
     */
    public static function additionalConfigKeys(): array
    {
        return [];
    }

    /**
     * Set the configuration for the provider.
     */
    public function setConfig($config): self
    {
        $configArray = $config->get();

        $this->clientId = $configArray['client_id'];
        $this->clientSecret = $configArray['client_secret'];
        $this->redirectUrl = $configArray['redirect'];

        return $this;
    }
}
