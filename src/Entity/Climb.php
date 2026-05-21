<?php

namespace App\Entity;

use App\Enum\Status;
use App\Repository\ClimbRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClimbRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Climb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'climbs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $climber = null;

    #[ORM\ManyToOne(inversedBy: 'climbs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'climbs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subcategory $subcategory = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $rank = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $score = null;

    #[ORM\Column(nullable: true)]
    private ?float $time = null;

    #[ORM\Column(nullable: true)]
    private ?float $height = null;

    #[ORM\Column(nullable: true)]
    private ?float $speed = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'string', enumType: Status::class)]
    private Status $status = Status::UNREVIEWED;

    #[ORM\Column]
    private ?bool $is_reviewed = null;

    #[ORM\ManyToOne(inversedBy: 'climbsVerified')]
    private ?User $verifier = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTime $updated_at;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $media_url = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClimber(): ?User
    {
        return $this->climber;
    }

    public function setClimber(?User $climber): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->climber = $climber;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->category = $category;

        return $this;
    }

    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->subcategory = $subcategory;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): static
    {
        //        if ($this->isReviewed()) {
        //            throw new \LogicException('This entry is locked.');
        //        }

        $this->score = $score;

        return $this;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setTime(?float $time): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->time = $time;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->height = $height;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setSpeed(?float $speed): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->speed = $speed;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->notes = $notes;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->status = $status;

        return $this;
    }

    public function isReviewed(): ?bool
    {
        return $this->is_reviewed;
    }

    public function setIsReviewed(bool $is_reviewed): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->is_reviewed = $is_reviewed;

        return $this;
    }

    public function getVerifier(): ?User
    {
        return $this->verifier;
    }

    public function setVerifier(?User $verifier): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->verifier = $verifier;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updated_at = new \DateTime();
    }

    public function getMediaUrl(): ?string
    {
        return $this->media_url;
    }

    public function setMediaUrl(string $media_url): static
    {
//        if ($this->isReviewed()) {
//            throw new \LogicException('This entry is locked.');
//        }

        $this->media_url = $media_url;

        return $this;
    }
}
