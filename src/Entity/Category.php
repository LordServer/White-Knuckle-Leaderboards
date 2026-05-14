<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
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
    private ?bool $is_archived = null;

    /**
     * @var Collection<int, Subcategory>
     */
    #[ORM\ManyToMany(targetEntity: Subcategory::class, inversedBy: 'categories')]
    private Collection $subcategory;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RankMethod $rank_method = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTime $updated_at;

    /**
     * @var Collection<int, Climb>
     */
    #[ORM\OneToMany(targetEntity: Climb::class, mappedBy: 'category')]
    private Collection $climbs;

    public function __construct()
    {
        $this->subcategory = new ArrayCollection();
        $this->climbs = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
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
        return $this->is_archived;
    }

    public function setIsArchived(bool $is_archived): static
    {
        $this->is_archived = $is_archived;

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

    public function getRankMethod(): ?RankMethod
    {
        return $this->rank_method;
    }

    public function setRankMethod(?RankMethod $rank_method): static
    {
        $this->rank_method = $rank_method;

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

    /**
     * @return Collection<int, Climb>
     */
    public function getClimbs(): Collection
    {
        return $this->climbs;
    }

    public function addClimb(Climb $climb): static
    {
        if (!$this->climbs->contains($climb)) {
            $this->climbs->add($climb);
            $climb->setCategory($this);
        }

        return $this;
    }

    public function removeClimb(Climb $climb): static
    {
        if ($this->climbs->removeElement($climb)) {
            // set the owning side to null (unless already changed)
            if ($climb->getCategory() === $this) {
                $climb->setCategory(null);
            }
        }

        return $this;
    }
}
