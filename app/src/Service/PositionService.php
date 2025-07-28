<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PositionService
{
    function __construct(private HttpClientInterface $client) {}

    public function getAvailablePositions(): array
    {
        $response = $this->client->request('GET', 'https://ibillboard.com/api/positions');

        if($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch positions');
        }
        return $response->toArray();
    }

    public function isValidPosition(string $position): bool
    {
        $positions = $this->getAvailablePositions();
        
        if (empty($positions)) {
            return false; // No positions available
        }

        return in_array($position, $positions['positions'], true);
    }
}