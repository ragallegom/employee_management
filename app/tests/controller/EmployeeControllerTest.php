<?php 

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeControllerTest extends WebTestCase
{
    public function testCreateEmployee(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/employees', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'John Doe',
            'email' => 'ana@correo.com',
            'position' => 'developer',
            'birthdate' => '1990-01-01',
        ]));

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('JWT Token not found', $responseData['message']);
        $this->assertEquals(401, $responseData['code']);
    }
}   