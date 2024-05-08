<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BadWordFilter
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = '3e33204cb468d4e21d789a40c7bd2080';
    }

    public function filterText(string $text): string
    {
        $response = $this->client->request('GET', 'https://api1.webpurify.com/services/rest/', [
            'query' => [
                'api_key' => $this->apiKey,
                'method' => 'webpurify.live.replace',
                'text' => $text,
                'replacesymbol' => '*',
                'format' => 'json'
            ]
        ]);

        $data = $response->toArray();
        return $data['text'] ?? $text;



        if ($response->getStatusCode() === 200) {
            $data = $response->toArray();
            if (isset($data['text'])) {
                return $data['text'];
            } else {
                error_log('No "text" field in the response: ' . print_r($data, true));
                return $text;  // Retourner le texte original si aucun champ 'text' n'est présent
            }
        } else {
            error_log('Failed to contact WebPurify: HTTP status ' . $response->getStatusCode());
            return $text;  // Gérer les erreurs de communication
        }
        
        

    }
}
