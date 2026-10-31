<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\SubscriptionType;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        CountryRepository $countryRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $user = new User();
        
        // Basic fields
        $user->setEmail($data['email'] ?? null);
        $user->setName($data['name'] ?? null);
        $user->setPhone($data['phone'] ?? null);
        
        // Country
        if (isset($data['countryId'])) {
            $country = $countryRepository->find($data['countryId']);
            if ($country) {
                $user->setCountry($country);
            } else {
                 return new JsonResponse(['error' => 'Country not found'], 400);
            }
        } else {
             return new JsonResponse(['error' => 'countryId is required'], 400);
        }

        // Password
        $plainPassword = $data['password'] ?? null;
        if (!$plainPassword) {
            return new JsonResponse(['error' => 'Password is required'], 400);
        }
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        // Defaults
        $user->setRoles(['ROLE_USER']);
        $user->setSubscriptionType(SubscriptionType::FREE);

        // Validation
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'User could not be created', 'details' => $e->getMessage()], 500);
        }

        return new JsonResponse([
            'message' => 'User created successfully',
            'userId' => $user->getId()->toString()
        ], 201);
    }
}
