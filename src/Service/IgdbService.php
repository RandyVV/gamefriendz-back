<?php

namespace App\Service;

use GuzzleHttp\Client as GuzzleClient;

class IgdbService
{
    private $clientId;
    private $clientSecret;
    private $twitchToken;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    private function connect()
    {
        // on prépare l'URL de l'API Twitch pour récupérer le token
        $url = 'https://id.twitch.tv/oauth2/token?client_id=' . $this->clientId
                . '&client_secret=' . $this->clientSecret
                . '&grant_type=client_credentials';

        // on fait la requête HTTP
        $httpClient = new GuzzleClient();
        $response = $httpClient->request('POST', $url);

        // si l'API nous répond avec un code 200, on récupère le token et on le garde en mémoire
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody()->getContents());
            $this->twitchToken = $data->access_token;
        }
        // sinon, on lance une erreur
        else {
            throw new \Exception('Error while connecting to Twitch API');
        }
    }

    private function request(string $endpoint, array $options): array
    {
        // on déduit l'URL de l'API en fonction de l'endpoint
        $url = 'https://api.igdb.com/v4/' . $endpoint;

        // on construit le corps de la requête
        $requestBody = '';
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $requestBody .= $key . ' ' . implode(',', $value) . ';';
            }
            else {
                $requestBody .= $key . ' ' . $value . ';';
            }
        }

        // on fait la requête HTTP
        $httpClient = new GuzzleClient();
        $response = $httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'text/plain',
                'Authorization' => 'Bearer ' . $this->twitchToken,
                'Client-ID' => $this->clientId
            ],
            'body' => $requestBody
        ]);

        // si l'API nous répond avec un code 200, on retourne les données
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody()->getContents());
            return $data;
        }
        // sinon, on lance une erreur
        else {
            throw new \Exception('IGDB API request error');
        }
    }

    public function getGames(int $limit = 500): array
    {
        // si on n'a pas encore de token, on se connecte
        if ($this->twitchToken === null) {
            $this->connect();
        }

        // on fait la requête pour récupérer les jeux
        $games = $this->request('games', [
            'fields' => [
                'name',
                'alternative_names.name',
                'alternative_names.comment',
                'cover.image_id',
                'release_dates.date',
                'release_dates.platform.name',
                'release_dates.platform.slug',
                'release_dates.platform.platform_logo.url',
                'genres.name',
                'summary',
                'multiplayer_modes',
            ],
            'limit' => $limit
        ]);

        return $games;
    }
}