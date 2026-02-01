<?php

namespace App\Tests\Api;

use App\Entity\Document;
use App\Entity\UsageQuota;
use App\Enum\SubscriptionType;
use App\Factory\CountryFactory;
use App\Factory\DocumentTypeFactory;
use App\Factory\TemplateFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class QuotaTest extends AuthenticationTest
{
    use ResetDatabase, Factories;

    public function testQuotaLimitEnforcement(): void
    {
        // 1. Create User with FREE subscription
        $user = UserFactory::createOne([
            'email' => 'free@example.com',
            'password' => 'password',
            'subscriptionType' => SubscriptionType::FREE,
        ]);

        $client = static::createClient();
        $token = $this->getToken($client, 'free@example.com', 'password');

        // 2. Create Dependencies
        $country = CountryFactory::createOne(['codeIso' => 'FR', 'name' => 'France']);
        $docType = DocumentTypeFactory::createOne(['slug' => 'contract', 'name' => 'Contrat']);
        $template = TemplateFactory::createOne([
            'country' => $country,
            'documentType' => $docType,
            'title' => 'Template Test'
        ]);

        // 3. Manually consume quota (simulate 3 documents generated)
        $quota = new UsageQuota();
        $quota->setUser($user->_real());
        $quota->setMonthYear((new \DateTimeImmutable())->format('Y-m'));
        $quota->setDocumentsGenerated(3); // Limit is 3

        // Persist quota
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($quota);
        $em->flush();

        // 4. Try to create a 4th document
        $client->request('POST', '/api/documents', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'template' => '/api/templates/' . $template->getId(),
                'inputData' => ['name' => 'Test']
            ]
        ]);

        // 5. Assert 422 Unprocessable Entity
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'description' => 'Vous avez atteint votre limite de documents pour ce mois (3/3). Passez au plan Pro pour illimité.'
        ]);
    }

    public function testQuotaNotExceeded(): void
    {
         // 1. Create User with FREE subscription
         $user = UserFactory::createOne([
            'email' => 'free2@example.com',
            'password' => 'password',
            'subscriptionType' => SubscriptionType::FREE,
        ]);

        $client = static::createClient();
        $token = $this->getToken($client, 'free2@example.com', 'password');

        // 2. Create Dependencies
        $country = CountryFactory::createOne(['codeIso' => 'FR', 'name' => 'France']);
        $docType = DocumentTypeFactory::createOne(['slug' => 'contract', 'name' => 'Contrat']);
        $template = TemplateFactory::createOne([
            'country' => $country,
            'documentType' => $docType,
            'title' => 'Template Test'
        ]);

        // 3. Quota is 0 by default. Create 1st document.
        $client->request('POST', '/api/documents', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'template' => '/api/templates/' . $template->getId(),
                'inputData' => ['name' => 'Test']
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
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
