<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Category;
use App\Entity\Climb;
use App\Entity\Subcategory;
use App\Entity\User;

#[ApiResource(
    shortName: 'Climb',
    stateOptions: new Options(entityClass: Climb::class),
)]
class ClimbApi
{
    public ?int $id = null;

    //    public ?User $climber = null;

    //    public ?Category $category = null;

    //    public ?Subcategory $subcategory = null;

    public ?string $rank = null;

    public ?string $score = null;

    public ?float $time = null;

    public ?float $height = null;

    public ?float $speed = null;

    public ?string $notes = null;

    public ?string $status = null;

    public ?bool $is_reviewed = null;

    //    public ?User $verifier = null;

    public ?\DateTimeImmutable $createdAt = null;

    public ?\DateTime $updatedAt = null;

    public ?string $media_url = null;
}
