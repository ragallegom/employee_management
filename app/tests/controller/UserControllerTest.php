<?php
 
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegisterUserSuccessfully(): void
    {
        $client = static::createClient();

        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $client->request('POST', '/api/register', [], [],
            [
                'CONTENT_TYPE' => 'application/json'
            ], json_encode([
                'email' => $email,
                'password' => $password
            ]
        ));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('User registered successfully', $data['message']);
    }

    public function testResgisterFailsWithInvalidEmail(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [],
            [
                'CONTENT_TYPE' => 'application/json'
            ], json_encode([
                'email' => 'INVALID',
                'password' => 'test123'
            ]
        ));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testResgisterFailsWithEmptyPassword(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [],
            [
                'CONTENT_TYPE' => 'application/json'
            ], json_encode([
                'email' => 'user_'. uniqid() . '@test.com',
                'password' => ''
            ]
        ));

        $this->assertResponseStatusCodeSame(400);
    }
}