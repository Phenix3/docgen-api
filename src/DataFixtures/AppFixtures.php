<?php

namespace App\DataFixtures;

use App\Enum\SubscriptionType;
use App\Factory\CountryFactory;
use App\Factory\DocumentFactory;
use App\Factory\DocumentTypeFactory;
use App\Factory\TemplateFactory;
use App\Factory\UsageQuotaFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Countries
        CountryFactory::createSequence([
            ['codeIso' => 'FR', 'name' => 'France'],
            ['codeIso' => 'US', 'name' => 'United States'],
            ['codeIso' => 'GB', 'name' => 'United Kingdom'],
            ['codeIso' => 'DE', 'name' => 'Germany'],
            ['codeIso' => 'ES', 'name' => 'Spain'],
        ]);

        // 2. Document Types
        DocumentTypeFactory::createSequence([
            ['slug' => 'cv', 'name' => 'Curriculum Vitae', 'description' => 'Professional resume', 'category' => 'career'],
            ['slug' => 'cover-letter', 'name' => 'Cover Letter', 'description' => 'Letter to accompany CV', 'category' => 'career'],
            ['slug' => 'contract', 'name' => 'Legal Contract', 'description' => 'Standard legal agreement', 'category' => 'legal'],
            ['slug' => 'report', 'name' => 'Business Report', 'description' => 'Formal business document', 'category' => 'business'],
        ]);

        // 3. Templates (linked to types)
        TemplateFactory::createMany(10, function() {
            return [
                'documentType' => DocumentTypeFactory::random(),
                'country' => CountryFactory::random(),
            ];
        });

        // 4. Users
        // Admin
        UserFactory::createOne([
            'email' => 'admin@docgen.com',
            'password' => 'password',
            'roles' => ['ROLE_ADMIN'],
            'name' => 'Admin User',
            'subscriptionType' => SubscriptionType::PRO,
            'country' => CountryFactory::random(),
        ]);

        // Standard User
        $user = UserFactory::createOne([
            'email' => 'user@docgen.com',
            'password' => 'password',
            'roles' => ['ROLE_USER'],
            'name' => 'John Doe',
            'subscriptionType' => SubscriptionType::FREE,
            'country' => CountryFactory::random(),
        ]);

        // Random Users
        UserFactory::createMany(10, function() {
            return ['country' => CountryFactory::random()];
        });

        // 5. Documents
        DocumentFactory::createMany(20, function() {
            return [
                'user' => UserFactory::random(),
                'template' => TemplateFactory::random(),
                'country' => CountryFactory::random(),
            ];
        });

        // 6. Quotas
        // Create quotas for the specific user
        UsageQuotaFactory::createOne([
            'user' => $user,
            'monthYear' => (new \DateTimeImmutable('first day of this month'))->format('Y-m'),
            'documentsGenerated' => 5,
        ]);

        // Random quotas
        UsageQuotaFactory::createMany(10, function() {
            return [
                'user' => UserFactory::random(),
            ];
        });
    }
}
