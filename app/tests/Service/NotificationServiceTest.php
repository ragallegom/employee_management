<?php

namespace App\Tests\Service;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NotificationServiceTest extends KernelTestCase
{
    private NotificationService $notificationService;
    
    protected function setUp(): void
    {
        self::bootKernel();
        $this->notificationService = self::getContainer()->get(
            NotificationService::class
        );
    }

    public function testNotifySuccessfully(): void
        {
            $payload = [
                'name' => 'John Doe',
                'email' => 'ana@correo.com',
                'position' => 'developer',
                'birthdate' => '1990-01-01',
            ];

            $this->notificationService->notify($payload);

            $this->assertTrue(true);
        }
}