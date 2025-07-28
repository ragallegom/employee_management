<?php 

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    private function registerUser(string $email, string $password): void
    {
        $this->client->request('POST', '/api/register', [], [],
            [
                'CONTENT_TYPE' => 'application/json'
            ], json_encode([
                'email' => $email,
                'password' => $password
            ]
        ));

        $this->assertResponseStatusCodeSame(201);
    }

    private function loginAndGetToken(string $email, string $password): string
    {
        $this->client->request('POST', '/api/login_check', [], [],
            [
                'CONTENT_TYPE' => 'application/json'
            ], json_encode([
                'email' => $email,
                'password' => $password
            ]));

        return json_decode($this->client->getResponse()->getContent(), true)['token'];
    }

    public function testCreateEmployeeWithoutAuthentication(): void
    {
        $this->client->request('POST', '/api/employees', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'John Doe',
            'email' => 'ana@correo.com',
            'position' => 'developer',
            'birthdate' => '1990-01-01',
        ]));

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('code', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('JWT Token not found', $responseData['message']);
        $this->assertEquals(401, $responseData['code']);
    }

    public function testCreateEmployeeSuccessfully(): void
    {
        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $this->registerUser($email, $password);
        $token = $this->loginAndGetToken($email, $password);

        $this->client->request('POST', '/api/employees', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ], json_encode([
                'name' => 'John Doe',
                'email' => 'ana@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('John Doe', $data['name']);
        $this->assertEquals('ana@correo.com', $data['email']);
        $this->assertEquals('product manager', $data['position']);
    }

    public function testCreateEmployeeWithoutName(): void
    {
        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $this->registerUser($email, $password);
        $token = $this->loginAndGetToken($email, $password);

        $this->client->request('POST', '/api/employees', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ], json_encode([
                'name' => '',
                'email' => 'ana@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals([
            'This value should not be blank.',
            'This value is too short. It should have 2 characters or more.'
        ], $data['errors']);
    }

    public function testCreateEmployeeWithoutEmail(): void
    {
        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $this->registerUser($email, $password);
        $token = $this->loginAndGetToken($email, $password);

        $this->client->request('POST', '/api/employees', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ], json_encode([
                'name' => 'John Dow',
                'email' => '',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals([
            'This value should not be blank.'
        ], $data['errors']);
    }

    public function testCreateEmployeeInvalidEmail(): void
    {
        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $this->registerUser($email, $password);
        $token = $this->loginAndGetToken($email, $password);

        $this->client->request('POST', '/api/employees', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ], json_encode([
                'name' => 'John Dow',
                'email' => 'invalid',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals([
            'This value is not a valid email address.'
        ], $data['errors']);
    }

    public function testCreateEmployeeInvalidPosition(): void
    {
        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $this->registerUser($email, $password);
        $token = $this->loginAndGetToken($email, $password);

        $this->client->request('POST', '/api/employees', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ], json_encode([
                'name' => 'John Dow',
                'email' => 'ana@correo.com',
                'position' => 'design',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid position', $data['error']);
    }
}   