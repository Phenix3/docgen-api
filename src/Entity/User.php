<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Enum\SubscriptionType;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
#[UniqueEntity(fields: ['phone'], message: 'Ce numéro de téléphone est déjà utilisé')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER') and object == user",
            securityMessage: "Vous ne pouvez accéder qu'à votre propre profil"
        ),
        new Put(
            security: "is_granted('ROLE_USER') and object == user",
            securityMessage: "Vous ne pouvez modifier que votre propre profil"
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['user:read', 'document:read'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'user:write'])]
    private Country $country;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['user:read', 'user:write', 'document:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true, nullable: true)]
    #[Assert\Email(message: 'Email invalide')]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, unique: true, nullable: true)]
    #[Assert\Regex(
        pattern: '/^\+?[0-9]{8,15}$/',
        message: 'Numéro de téléphone invalide'
    )]
    #[Groups(['user:read', 'user:write'])]
    private ?string $phone = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', enumType: SubscriptionType::class)]
    #[Groups(['user:read'])]
    private SubscriptionType $subscriptionType = SubscriptionType::FREE;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $subscriptionEndsAt = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private bool $isGuest = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $emailVerifiedAt = null;

    /** @var Collection<int, Document> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Document::class, orphanRemoval: true)]
    private Collection $documents;

    /** @var Collection<int, UsageQuota> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UsageQuota::class, orphanRemoval: true)]
    private Collection $usageQuotas;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->documents = new ArrayCollection();
        $this->usageQuotas = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles temporaires si nécessaire
    }

    public function getSubscriptionType(): SubscriptionType
    {
        return $this->subscriptionType;
    }

    public function setSubscriptionType(SubscriptionType $subscriptionType): static
    {
        $this->subscriptionType = $subscriptionType;
        return $this;
    }

    public function getSubscriptionEndsAt(): ?\DateTimeImmutable
    {
        return $this->subscriptionEndsAt;
    }

    public function setSubscriptionEndsAt(?\DateTimeImmutable $subscriptionEndsAt): static
    {
        $this->subscriptionEndsAt = $subscriptionEndsAt;
        return $this;
    }

    public function isGuest(): bool
    {
        return $this->isGuest;
    }

    public function setIsGuest(bool $isGuest): static
    {
        $this->isGuest = $isGuest;
        return $this;
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(?\DateTimeImmutable $emailVerifiedAt): static
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setUser($this);
        }
        return $this;
    }

    public function removeDocument(Document $document): static
    {
        $this->documents->removeElement($document);
        return $this;
    }

    /**
     * @return Collection<int, UsageQuota>
     */
    public function getUsageQuotas(): Collection
    {
        return $this->usageQuotas;
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

    public function getMonthlyLimit(): int
    {
        return $this->subscriptionType->monthlyLimit();
    }

    public function canExportDocx(): bool
    {
        return $this->subscriptionType->canExportDocx();
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->subscriptionType === SubscriptionType::FREE) {
            return true;
        }
        
        return $this->subscriptionEndsAt !== null && $this->subscriptionEndsAt > new \DateTimeImmutable();
    }
}
