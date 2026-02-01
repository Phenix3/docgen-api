<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\UsageQuotaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UsageQuotaRepository::class)]
#[ORM\Table(name: 'usage_quota')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            securityMessage: "Vous ne pouvez consulter que vos propres quotas"
        ),
    ],
    normalizationContext: ['groups' => ['quota:read']]
)]
class UsageQuota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['quota:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'usageQuotas')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['quota:read'])]
    private User $user;

    #[ORM\Column(length: 7)]
    #[Groups(['quota:read', 'user:read'])]
    private string $monthYear; // Format: YYYY-MM

    #[ORM\Column]
    #[Groups(['quota:read', 'user:read'])]
    private int $documentsGenerated = 0;

    #[ORM\Column]
    #[Groups(['quota:read', 'user:read'])]
    private int $docxExports = 0;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->monthYear = (new \DateTimeImmutable())->format('Y-m');
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getMonthYear(): string
    {
        return $this->monthYear;
    }

    public function setMonthYear(string $monthYear): static
    {
        $this->monthYear = $monthYear;
        return $this;
    }

    public function getDocumentsGenerated(): int
    {
        return $this->documentsGenerated;
    }

    public function setDocumentsGenerated(int $documentsGenerated): static
    {
        $this->documentsGenerated = $documentsGenerated;
        return $this;
    }

    public function incrementDocumentsGenerated(): static
    {
        $this->documentsGenerated++;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getDocxExports(): int
    {
        return $this->docxExports;
    }

    public function setDocxExports(int $docxExports): static
    {
        $this->docxExports = $docxExports;
        return $this;
    }

    public function incrementDocxExports(): static
    {
        $this->docxExports++;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
