<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TestLegifranceApiService
{
    private $clientId;
    private $clientSecret;
    private $tokenUrl = 'https://sandbox-oauth.piste.gouv.fr/api/oauth/token';
    private $httpClient;
    private $client;

    public function __construct()
    {
        $this->clientId = $_ENV['OAUTH_ID'];
        $this->clientSecret = $_ENV['OAUTH_SECRET'];
        $this->httpClient = HttpClient::create();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getAccessToken(): string
    {
        $response = $this->httpClient->request('POST', $this->tokenUrl, [
            'body' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'openid',
            ],
        ]);

        // Vérification de la réponse
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to retrieve access token');
        }

        $data = $response->toArray();

        return $data['access_token'];
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function requestApiData(string $endpoint): array
    {
        $accessToken = $this->getAccessToken();

        $response = $this->httpClient->request('GET', $endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to retrieve data from API');
        }

        return $response->toArray();
    }

}


