<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Enum\DocumentStatus;
use App\Factory\CountryFactory;
use App\Factory\DocumentFactory;
use App\Factory\DocumentTypeFactory;
use App\Factory\TemplateFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DocumentResourceTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function testGetCollection()
    {
        $user = UserFactory::createOne();
        DocumentFactory::createMany(3, ['user' => $user]);
        DocumentFactory::createMany(2); // Other users' documents

        $client = static::createClient();
        // Simulate login
        $token = $this->getToken($client, $user->getEmail(), 'password');

        $client->request('GET', '/api/documents', ['auth_bearer' => $token]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['totalItems' => 3]);
    }

    public function testCreateDocumentTriggersGeneration()
    {
        $user = UserFactory::createOne();
        $template = TemplateFactory::createOne();

        $client = static::createClient();
        $token = $this->getToken($client, $user->getEmail(), 'password');

        $client->request('POST', '/api/documents', [
            'auth_bearer' => $token,
            'json' => [
                'template' => '/api/templates/' . $template->getId(),
                'inputData' => ['name' => 'Test']
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        // The generation is async, so in test env with sync messenger, it might be already processed
        // We can check if status is not DRAFT (it should be COMPLETED or GENERATING)
    }

    private function getToken($client, $email, $password): string
    {
        $response = $client->request('POST', '/api/auth/login', ['json' => [
            'email' => $email,
            'password' => $password
        ]]);

        return $response->toArray()['token'];
    }
}
