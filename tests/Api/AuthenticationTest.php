<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\CountryFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthenticationTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function testRegistration()
    {
        $country = CountryFactory::createOne(['codeIso' => 'FR']);

        $client = static::createClient();
        $client->request('POST', '/api/auth/register', ['json' => [
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'name' => 'New User',
            'countryCode' => 'FR'
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['message' => 'User created successfully']);
    }

    public function testLogin()
    {
        $user = UserFactory::createOne([
            'email' => 'user@test.com',
            'password' => 'password',
        ]);

        $client = static::createClient();
        $response = $client->request('POST', '/api/auth/login', ['json' => [
            'email' => 'user@test.com',
            'password' => 'password'
        ]]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertArrayHasKey('token', $data);
    }
}
