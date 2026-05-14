<?php

namespace App\Entity;

use App\Repository\SubcategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubcategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Subcategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTime $updated_at = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'subcategory')]
    private Collection $categories;

    /**
     * @var Collection<int, Climb>
     */
    #[ORM\OneToMany(targetEntity: Climb::class, mappedBy: 'subcategory')]
    private Collection $climbs;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addSubcategory($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeSubcategory($this);
        }

        return $this;
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
            $climb->setSubcategory($this);
        }

        return $this;
    }

    public function removeClimb(Climb $climb): static
    {
        if ($this->climbs->removeElement($climb)) {
            // set the owning side to null (unless already changed)
            if ($climb->getSubcategory() === $this) {
                $climb->setSubcategory(null);
            }
        }

        return $this;
    }
}
