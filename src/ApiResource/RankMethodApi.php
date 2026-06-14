<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata;
use App\Entity\RankMethod;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Metadata\ApiResource(
    shortName: 'RankMethod',
    operations: [
        new Metadata\Get(
            security: 'is_granted("rank_method_api_read", object)',
        ),
        new Metadata\GetCollection(
            security: 'is_granted("rank_method_api_list", object)',
        ),
        new Metadata\Post(
            security: 'is_granted("rank_method_api_create", object)',
        ),
        new Metadata\Patch(
            security: 'is_granted("rank_method_api_update", object)',
        ),
        new Metadata\Delete(
            security: 'is_granted("rank_method_api_delete", object)',
        ),
    ],
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: RankMethod::class),
)]
class RankMethodApi
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

    #[Metadata\ApiProperty(writable: false)]
    public ?\DateTimeImmutable $created_at = null;

    #[Metadata\ApiProperty(writable: false)]
    public ?\DateTime $updated_at = null;
}
