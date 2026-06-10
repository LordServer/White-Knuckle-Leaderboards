<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\RankMethod;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'RankMethod',
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: RankMethod::class),
)]
class RankMethodApi
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

    #[ApiProperty(writable: false)]
    public ?\DateTimeImmutable $created_at = null;

    #[ApiProperty(writable: false)]
    public ?\DateTime $updated_at = null;
}
