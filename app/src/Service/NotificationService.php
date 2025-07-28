<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $notificationUrl
    ){}

    public function notify(array $payload): void
    {
        $response = $this->httpClient->request('POST', $this->notificationUrl, [
            'json' => $payload,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Notification failed: ' . $response->getContent(false));
        }
    }
    
}