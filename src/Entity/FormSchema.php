<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'form_schema')]
class FormSchema
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, unique: true)]
    private string $id;

    #[ORM\Column(type: Types::JSON)]
    private array $jsonStructure;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $title;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastAccessedAt = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $accessCount = 0;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getJsonStructure(): array
    {
        return $this->jsonStructure;
    }

    public function setJsonStructure(array $jsonStructure): self
    {
        $this->jsonStructure = $jsonStructure;
        
        // Extraction automatique du titre
        $this->title = $jsonStructure['title'] ?? 'Formulaire sans titre';
        
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastAccessedAt(): ?\DateTimeImmutable
    {
        return $this->lastAccessedAt;
    }

    public function setLastAccessedAt(\DateTimeImmutable $lastAccessedAt): self
    {
        $this->lastAccessedAt = $lastAccessedAt;
        return $this;
    }

    public function getAccessCount(): int
    {
        return $this->accessCount;
    }

    public function incrementAccessCount(): self
    {
        $this->accessCount++;
        $this->lastAccessedAt = new \DateTimeImmutable();
        return $this;
    }
}
