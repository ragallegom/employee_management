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
            'name' => 'John',
            'lastName' => 'Doe',
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
                'name' => 'John',
                'lastName' => 'Doe',
                'email' => 'ana@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('John', $data['name']);
        $this->assertEquals('Doe', $data['lastName']);
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
                'lastName' => 'Test',
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
                'name' => 'John',
                'lastName' => 'Dow',
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
                'name' => 'John',
                'lastName' => 'Dow',
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
                'name' => 'John',
                'lastName' => 'Dow',
                'email' => 'ana@correo.com',
                'position' => 'design',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid position', $data['error']);
    }

    public function testShowEmployee(): void
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
                'name' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $id = $data['id'];

        $this->client->request('GET', "/api/employees/$id", [], [],
            [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('John', $data['name']);
        $this->assertEquals('Doe', $data['lastName']);
        $this->assertEquals('john@correo.com', $data['email']);
        $this->assertEquals('product manager', $data['position']);
    }

    public function testUpdateEmployee(): void
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
                'name' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('John', $data['name']);
        $this->assertEquals('Doe', $data['lastName']);
        $this->assertEquals('john@correo.com', $data['email']);
        $this->assertEquals('product manager', $data['position']);

        $id = $data['id'];
        
        $this->client->request('PUT', "/api/employees/$id", [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ], json_encode([
                'name' => 'Ana',
                'lastName' => 'Update',
                'email' => 'john@correo.com',
                'position' => 'help desk',
                'birthDate' => '1990-01-01',
            ]));

        

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Ana', $data['name']);
        $this->assertEquals('Update', $data['lastName']);
        $this->assertEquals('john@correo.com', $data['email']);
        $this->assertEquals('help desk', $data['position']);
    }

    public function testUpdateEmployeenNotAllowed(): void
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
                'name' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('John', $data['name']);
        $this->assertEquals('Doe', $data['lastName']);
        $this->assertEquals('john@correo.com', $data['email']);
        $this->assertEquals('product manager', $data['position']);

        $id = $data['id'];

        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $this->registerUser($email, $password);
        $token = $this->loginAndGetToken($email, $password);
        
        $this->client->request('PUT', "/api/employees/$id", [], [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Bearer ' . $token,
            ], json_encode([
                'name' => 'John Update',
                'email' => 'john@correo.com',
                'position' => 'help desk',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(403);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('You do not have permission to update this employee', $data['error']);
    }

    public function testDeleteEmployee(): void
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
                'name' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('John', $data['name']);
        $this->assertEquals('Doe', $data['lastName']);
        $this->assertEquals('john@correo.com', $data['email']);
        $this->assertEquals('product manager', $data['position']);

        $id = $data['id'];

        $this->client->request('DELETE', "/api/employees/$id", [], [],
            [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]
        );

        $this->assertResponseStatusCodeSame(204);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNull($data);
        
        $this->client->request('GET', "/api/employees/$id", [], [],
            [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteEmployeeNotAllowed(): void
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
                'name' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals('John', $data['name']);
        $this->assertEquals('Doe', $data['lastName']);
        $this->assertEquals('john@correo.com', $data['email']);
        $this->assertEquals('product manager', $data['position']);

        $id = $data['id'];

        $email = 'user_'. uniqid() . '@test.com';
        $password = 'test13';

        $this->registerUser($email, $password);
        $token = $this->loginAndGetToken($email, $password);

        $this->client->request('DELETE', "/api/employees/$id", [], [],
            [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]
        );

        $this->assertResponseStatusCodeSame(403);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('You do not have permission to delete this employee', $data['error']);
    }

    public function testShowEmployeeByName(): void
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
                'name' => 'John',
                'lastName' => 'Doe',
                'email' => 'john@correo.com',
                'position' => 'product manager',
                'birthDate' => '1990-01-01',
            ]));

        $this->assertResponseStatusCodeSame(201);

        $this->client->request('GET', "/api/employees/search", 
            [
                'name' => 'John'
            ], [],
            [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        $this->assertEquals('John', $data[0]['name']);

        foreach ($data as $employee) {
            $this->assertArrayHasKey('id', $employee);
            $this->assertArrayHasKey('name', $employee);
            $this->assertArrayHasKey('email', $employee);
            $this->assertArrayHasKey('position', $employee);
            $this->assertArrayHasKey('birthDate', $employee);
        }
    }
}


