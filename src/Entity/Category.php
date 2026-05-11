<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $rules = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subcategory $subcategory = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RankMethod $rank_method = null;

    #[ORM\Column]
    private ?bool $is_archived = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created = null;

    #[ORM\Column]
    private ?\DateTime $modified = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRules(): ?string
    {
        return $this->rules;
    }

    public function setRules(string $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): static
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    public function getRankMethod(): ?RankMethod
    {
        return $this->rank_method;
    }

    public function setRankMethod(?RankMethod $rank_method): static
    {
        $this->rank_method = $rank_method;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->is_archived;
    }

    public function setIsArchived(bool $is_archived): static
    {
        $this->is_archived = $is_archived;

        return $this;
    }

    public function getCreated(): ?\DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(\DateTimeImmutable $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getModified(): ?\DateTime
    {
        return $this->modified;
    }

    public function setModified(\DateTime $modified): static
    {
        $this->modified = $modified;

        return $this;
    }
}
