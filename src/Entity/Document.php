<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Enum\DocumentStatus;
use App\Enum\ToneLevel;
use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use App\State\DocumentPostProcessor;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table(name: 'document')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous devez être connecté pour voir vos documents"
        ),
        new Get(
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            securityMessage: "Vous ne pouvez accéder qu'à vos propres documents"
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            denormalizationContext: ['groups' => ['document:create']],
            securityMessage: "Vous devez être connecté pour créer un document",
            processor: DocumentPostProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_USER') and object.getUser() == user and object.isEditable()",
            denormalizationContext: ['groups' => ['document:write']],
            securityMessage: "Vous ne pouvez modifier que vos propres documents brouillons"
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            securityMessage: "Vous ne pouvez supprimer que vos propres documents"
        )
    ],
    normalizationContext: ['groups' => ['document:read']],
    order: ['createdAt' => 'DESC'],
    paginationEnabled: true,
    paginationItemsPerPage: 20
)]
class Document
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['document:read'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Template::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:read', 'document:create'])]
    private Template $template;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:read', 'document:create'])]
    private Country $country;

    #[ORM\ManyToOne(targetEntity: DocumentType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:read'])]
    private DocumentType $documentType;

    #[ORM\Column(type: 'string', enumType: DocumentStatus::class)]
    #[Groups(['document:read'])]
    private DocumentStatus $status = DocumentStatus::DRAFT;

    #[ORM\Column(type: 'string', enumType: ToneLevel::class)]
    #[Groups(['document:read', 'document:create', 'document:write'])]
    private ToneLevel $tone = ToneLevel::FORMAL;

    #[ORM\Column]
    #[Groups(['document:read', 'document:create', 'document:write'])]
    private array $inputData = [];

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['document:read'])]
    private ?string $generatedContent = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?string $pdfPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?string $docxPath = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?string $errorMessage = null;

    #[ORM\Column]
    #[Groups(['document:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?\DateTimeImmutable $generatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
        $this->user = null;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): static
    {
        $this->template = $template;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getDocumentType(): DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType(DocumentType $documentType): static
    {
        $this->documentType = $documentType;
        return $this;
    }

    public function getStatus(): DocumentStatus
    {
        return $this->status;
    }

    public function setStatus(DocumentStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getTone(): ToneLevel
    {
        return $this->tone;
    }

    public function setTone(ToneLevel $tone): static
    {
        $this->tone = $tone;
        return $this;
    }

    public function getInputData(): array
    {
        return $this->inputData;
    }

    public function setInputData(array $inputData): static
    {
        $this->inputData = $inputData;
        return $this;
    }

    public function getGeneratedContent(): ?string
    {
        return $this->generatedContent;
    }

    public function setGeneratedContent(?string $generatedContent): static
    {
        $this->generatedContent = $generatedContent;
        return $this;
    }

    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(?string $pdfPath): static
    {
        $this->pdfPath = $pdfPath;
        return $this;
    }

    public function getDocxPath(): ?string
    {
        return $this->docxPath;
    }

    public function setDocxPath(?string $docxPath): static
    {
        $this->docxPath = $docxPath;
        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getGeneratedAt(): ?\DateTimeImmutable
    {
        return $this->generatedAt;
    }

    public function setGeneratedAt(?\DateTimeImmutable $generatedAt): static
    {
        $this->generatedAt = $generatedAt;
        return $this;
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    #[ORM\PrePersist]
    public function setDefaults(): void
    {
        // Si le template est défini mais pas le type de document ou le pays, on les récupère du template
        if (isset($this->template)) {
            if (!isset($this->documentType)) {
                $this->documentType = $this->template->getDocumentType();
            }
            if (!isset($this->country)) {
                $this->country = $this->template->getCountry();
            }
        }
    }
}
