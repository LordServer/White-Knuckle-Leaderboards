<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column]
    private ?bool $isArchived = null;

    /**
     * @var Collection<int, Subcategory>
     */
    #[ORM\ManyToMany(targetEntity: Subcategory::class, inversedBy: 'categories')]
    private Collection $subcategory;

    /**
     * @var Collection<int, RankMethod>
     */
    #[ORM\ManyToMany(targetEntity: RankMethod::class, inversedBy: 'categories')]
    private Collection $rankMethod;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->subcategory = new ArrayCollection();
        $this->rankMethod = new ArrayCollection();
    }

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

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * @return Collection<int, Subcategory>
     */
    public function getSubcategory(): Collection
    {
        return $this->subcategory;
    }

    public function addSubcategory(Subcategory $subcategory): static
    {
        if (!$this->subcategory->contains($subcategory)) {
            $this->subcategory->add($subcategory);
        }

        return $this;
    }

    public function removeSubcategory(Subcategory $subcategory): static
    {
        $this->subcategory->removeElement($subcategory);

        return $this;
    }

    /**
     * @return Collection<int, RankMethod>
     */
    public function getRankMethod(): Collection
    {
        return $this->rankMethod;
    }

    public function addRankMethod(RankMethod $rankMethod): static
    {
        if (!$this->rankMethod->contains($rankMethod)) {
            $this->rankMethod->add($rankMethod);
        }

        return $this;
    }

    public function removeRankMethod(RankMethod $rankMethod): static
    {
        $this->rankMethod->removeElement($rankMethod);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
