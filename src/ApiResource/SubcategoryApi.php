<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata;
use App\Entity\Subcategory;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Metadata\ApiResource(
    shortName: 'Subcategory',
    operations: [
        new Metadata\Get(
            security: 'is_granted("subcategory_api_read", object)',
        ),
        new Metadata\GetCollection(
            security: 'is_granted("subcategory_api_list", object)',
        ),
        new Metadata\Post(
            security: 'is_granted("subcategory_api_create", object)',
        ),
        new Metadata\Patch(
            security: 'is_granted("subcategory_api_update", object)',
        ),
        new Metadata\Delete(
            security: 'is_granted("subcategory_api_delete", object)',
        ),
    ],
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: Subcategory::class),
)]
class SubcategoryApi
{
    #[Metadata\ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    public ?string $name = null;

    /**
     * @var array<int, CategoryApi>
     */
    #[Metadata\ApiProperty(writable: false)]
    #[MaxDepth(1)]
    public array $categories = [];

    /**
     * @var array<int, ClimbApi>
     */
    #[Metadata\ApiProperty(writable: false)]
    #[MaxDepth(1)]
    public array $climbs = [];

    #[Metadata\ApiProperty(writable: false)]
    public ?\DateTimeImmutable $created_at = null;

    #[Metadata\ApiProperty(writable: false)]
    public ?\DateTime $updated_at = null;
}
