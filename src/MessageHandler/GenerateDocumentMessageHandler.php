<?php

namespace App\MessageHandler;

use App\Entity\Document;
use App\Enum\DocumentStatus;
use App\Message\GenerateDocumentMessage;
use App\Repository\DocumentRepository;
use App\Service\DocumentContentGenerator;
use App\Service\PdfGeneratorService;
use App\Service\QuotaService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsMessageHandler]
class GenerateDocumentMessageHandler
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentContentGenerator $contentGenerator,
        private PdfGeneratorService $pdfGenerator,
        private QuotaService $quotaService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        #[Autowire('%kernel.project_dir%/public/uploads/documents')] private string $documentsDir
    ) {
    }

    public function __invoke(GenerateDocumentMessage $message): void
    {
        $documentId = $message->getDocumentId();
        $document = $this->documentRepository->find($documentId);

        if (!$document) {
            $this->logger->error("Document not found for generation: {$documentId}");
            return;
        }

        try {
            // Update status to generating
            $document->setStatus(DocumentStatus::GENERATING);
            $this->entityManager->flush();

            // 1. Generate Content
            $htmlContent = $this->contentGenerator->generate($document);
            $document->setGeneratedContent($htmlContent);
            $document->setGeneratedAt(new \DateTimeImmutable());

            // 2. Generate PDF
            $slug = $document->getDocumentType()->getSlug();
            $fileName = sprintf('%s_%s.pdf', $slug, $document->getId()->toString());
            // Ensure directory exists
            if (!file_exists($this->documentsDir)) {
                mkdir($this->documentsDir, 0777, true);
            }

            $filePath = $this->documentsDir . DIRECTORY_SEPARATOR . $fileName;
            $this->pdfGenerator->generatePdfFromHtml($htmlContent, $filePath);

            // 3. Update Document
            $document->setPdfPath('uploads/documents/' . $fileName);
            $document->setStatus(DocumentStatus::COMPLETED);

            // Increment user quota
            $this->quotaService->incrementQuota($document->getUser());

            $this->entityManager->flush();
            $this->logger->info("Document {$documentId} generated successfully.");

        } catch (\Exception $e) {
            $this->logger->error("Failed to generate document {$documentId}: " . $e->getMessage());

            // Refresh document to discard any invalid state that might have caused the flush error
            $this->entityManager->refresh($document);

            $document->setStatus(DocumentStatus::FAILED);
            $document->setErrorMessage(substr($e->getMessage(), 0, 255));
            $this->entityManager->flush();
            throw $e; // Retry mechanism can pick this up
        }
    }
}
