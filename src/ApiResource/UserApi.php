<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\User;
use App\Enum\UserStatus;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'User',
    paginationItemsPerPage: 5,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: User::class),
)]
#[ApiFilter(SearchFilter::class, properties: [
    'username' => 'partial',
])]
class UserApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    public ?string $username = null;

    #[NotBlank]
    public ?string $display_name = null;

    #[NotBlank]
    public ?string $discord_id = null;

    #[NotBlank]
    public ?string $avatar = null;

    #[NotBlank]
    public ?UserStatus $status = null;

    public ?\DateTime $bannedUntil = null;

    #[ApiProperty(writable: false)]
    public ?array $roles = null;

    /**
     * @var array<int, ClimbApi>
     */
    #[ApiProperty(writable: false)]
    #[MaxDepth(1)]
    public array $climbs = [];

    /**
     * @var array<int, ClimbApi>
     */
    #[ApiProperty(writable: false)]
    #[MaxDepth(1)]
    public array $climbsVerified = [];

    #[ApiProperty(writable: false)]
    public ?\DateTimeImmutable $createdAt = null;

    #[ApiProperty(writable: false)]
    public ?\DateTime $updatedAt = null;
}
