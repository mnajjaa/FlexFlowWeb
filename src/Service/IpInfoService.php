<?php 

namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IpInfoService
{
    private $client;
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    public function getIpInfo(string $ip): array
    {
        $response = $this->client->request('GET', 'https://ipinfo.io/'.$ip.'?token=f148d970d1b548');
        return $response->toArray();
    }
}