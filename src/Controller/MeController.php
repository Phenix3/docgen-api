<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MeController extends AbstractController
{
    public function __invoke(SerializerInterface $serializer): JsonResponse
    {
        /** @var UserInterface|null $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        // Use serialization groups to return relevant data
        $json = $serializer->serialize($user, 'json', ['groups' => ['user:read']]);

        return new JsonResponse($json, 200, [], true);
    }
}
