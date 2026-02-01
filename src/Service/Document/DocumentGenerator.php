<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Document;
use App\Enum\DocumentStatus;
use App\Exception\AIGenerationException;
use App\Exception\DocumentNotEditableException;
use App\Service\AI\AIServiceInterface;
use App\Service\AI\PromptBuilder;
use App\Service\QuotaService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class DocumentGenerator
{
    public function __construct(
        private readonly AIServiceInterface $aiService,
        private readonly PromptBuilder $promptBuilder,
        private readonly QuotaService $quotaService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {}

    public function generate(Document $document): void
    {
        // 1. Validation initiale
        if ($document->getStatus() !== DocumentStatus::DRAFT) {
            throw new DocumentNotEditableException(
                sprintf('Le document %d ne peut pas être généré car son statut est %s',
                $document->getId(),
                $document->getStatus()->value)
            );
        }

        // 2. Vérification des quotas (double check avant génération coûteuse)
        $user = $document->getUser();
        if (!$this->quotaService->checkQuota($user)) {
             $document->setStatus(DocumentStatus::FAILED);
             $this->entityManager->flush();
             // On pourrait lancer une exception, mais ici on veut juste marquer le doc comme failed
             $this->logger->warning("Quota exceeded for user {$user->getId()} during generation of document {$document->getId()}");
             return;
        }

        try {
            $document->setStatus(DocumentStatus::GENERATING);
            $this->entityManager->flush();

            $template = $document->getTemplate();

            // 3. Construction des prompts
            // Si le template n'a pas de prompt spécifique, on utilise un fallback ou on lève une erreur
            // Pour l'instant on suppose que le contentStructure est le prompt utilisateur si userPromptTemplate est vide
            $systemPrompt = $template->getSystemPrompt() ?? 'Tu es un assistant juridique expert. Génère le document au format HTML propre sans balises markdown.';
            $userPromptTemplate = $template->getUserPromptTemplate() ?? $template->getContentStructure();

            $userPrompt = $this->promptBuilder->build(
                $userPromptTemplate,
                $document->getInputData()
            );

            // 4. Appel IA
            $response = $this->aiService->generate($systemPrompt, $userPrompt);

            if (!$response->success) {
                throw new AIGenerationException($response->error ?? 'Erreur inconnue');
            }

            // 5. Mise à jour du document
            $document->setGeneratedContent($response->content);
            $document->setGeneratedAt(new \DateTimeImmutable());

            // On ne met pas encore en COMPLETED, car il reste l'étape PDF
            // Mais dans l'architecture actuelle, c'est le handler qui gère la suite (PDF)
            // Donc ici on a fini la partie "Génération de contenu"

            $this->logger->info("Contenu généré pour le document {$document->getId()}", [
                'tokens' => $response->tokensUsed,
                'duration' => $response->durationMs
            ]);

        } catch (\Throwable $e) {
            $document->setStatus(DocumentStatus::FAILED);
            $document->setErrorMessage($e->getMessage());
            $this->entityManager->flush();

            throw $e;
        }
    }
}
