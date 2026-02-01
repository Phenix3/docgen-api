<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DownloadDocumentController extends AbstractController
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/')] private string $publicDir
    ) {
    }

    public function __invoke(string $id, EntityManagerInterface $entityManager): BinaryFileResponse|JsonResponse
    {
        $document = $entityManager->getRepository(Document::class)->find($id);

        if (!$document) {
            throw new NotFoundHttpException('Document not found');
        }

        // Check permissions: User must be owner
        /** @var User $user */
        $user = $this->getUser();
        if ($document->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
             return new JsonResponse(['error' => 'Access denied'], 403);
        }

        if (!$document->getPdfPath()) {
            return new JsonResponse(['error' => 'Document not yet generated'], 404);
        }

        $filePath = $this->publicDir . $document->getPdfPath();

        if (!file_exists($filePath)) {
            return new JsonResponse(['error' => 'File not found on server'], 404);
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($filePath)
        );

        return $response;
    }
}
