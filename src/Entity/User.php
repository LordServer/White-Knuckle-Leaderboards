<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $discord_id = null;

    #[ORM\Column(length: 255)]
    private ?string $avatar = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTime $updated_at;

    /**
     * @var Collection<int, Climb>
     */
    #[ORM\OneToMany(targetEntity: Climb::class, mappedBy: 'climber', orphanRemoval: true)]
    private Collection $climbs;

    /**
     * @var Collection<int, Climb>
     */
    #[ORM\OneToMany(targetEntity: Climb::class, mappedBy: 'verifier')]
    private Collection $climbsVerified;

    #[ORM\Column(length: 255)]
    private ?string $display_name = null;

    public function __construct()
    {
        $this->climbs = new ArrayCollection();
        $this->climbsVerified = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): static
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): static
    {
        $role = strtoupper($role);
        $key = array_search($role, $this->roles, true);
        if (false !== $key) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function getDiscordId(): ?string
    {
        return $this->discord_id;
    }

    public function setDiscordId(string $discord_id): static
    {
        $this->discord_id = $discord_id;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

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
            $climb->setClimber($this);
        }

        return $this;
    }

    public function removeClimb(Climb $climb): static
    {
        if ($this->climbs->removeElement($climb)) {
            // set the owning side to null (unless already changed)
            if ($climb->getClimber() === $this) {
                $climb->setClimber(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Climb>
     */
    public function getClimbsVerified(): Collection
    {
        return $this->climbsVerified;
    }

    public function addClimbsVerified(Climb $climbsVerified): static
    {
        if (!$this->climbsVerified->contains($climbsVerified)) {
            $this->climbsVerified->add($climbsVerified);
            $climbsVerified->setVerifier($this);
        }

        return $this;
    }

    public function removeClimbsVerified(Climb $climbsVerified): static
    {
        if ($this->climbsVerified->removeElement($climbsVerified)) {
            // set the owning side to null (unless already changed)
            if ($climbsVerified->getVerifier() === $this) {
                $climbsVerified->setVerifier(null);
            }
        }

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayName(string $display_name): static
    {
        $this->display_name = $display_name;

        return $this;
    }
}
