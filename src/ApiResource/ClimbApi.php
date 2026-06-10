<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Climb;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Climb',
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: Climb::class),
)]
class ClimbApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    public ?string $media_url = null;

    #[NotBlank]
    public ?CategoryApi $category = null;

    #[NotBlank]
    public ?SubcategoryApi $subcategory = null;

    #[ApiProperty(writable: false)]
    public ?string $rank = null;

    #[NotBlank]
    public ?string $score = null;

    #[NotBlank]
    public ?float $time = null;

    #[NotBlank]
    public ?float $height = null;

    #[NotBlank]
    public ?float $speed = null;

    #[NotBlank]
    #[MaxDepth(1)]
    public ?UserApi $climber = null;

    #[NotBlank]
    public ?string $status = null;

    #[NotBlank]
    public ?bool $is_reviewed = null;

    #[MaxDepth(1)]
    public ?UserApi $verifier = null;

    public ?string $notes = null;

    #[ApiProperty(writable: false)]
    public ?\DateTimeImmutable $createdAt = null;

    #[ApiProperty(writable: false)]
    public ?\DateTime $updatedAt = null;
}
