<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Subcategory;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Subcategory',
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: Subcategory::class),
)]
class SubcategoryApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    public ?string $name = null;

    /**
     * @var array<int, CategoryApi>
     */
    #[ApiProperty(writable: false)]
    #[MaxDepth(1)]
    public array $categories = [];

    /**
     * @var array<int, ClimbApi>
     */
    #[ApiProperty(writable: false)]
    #[MaxDepth(1)]
    public array $climbs = [];

    #[ApiProperty(writable: false)]
    public ?\DateTimeImmutable $created_at = null;

    #[ApiProperty(writable: false)]
    public ?\DateTime $updated_at = null;
}
