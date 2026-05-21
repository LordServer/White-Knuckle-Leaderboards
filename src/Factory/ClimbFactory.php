<?php

namespace App\Factory;

use App\Entity\Category;
use App\Entity\Climb;
use App\Entity\Subcategory;
use App\Enum\Status;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Climb>
 */
final class ClimbFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Climb::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        $time = self::faker()->randomFloat(2, 331.02, 5501.75);
        $height = self::faker()->randomFloat(2, 3565.65, 84129.47);

        $isReviewed = self::faker()->boolean();

        $createdAt = self::faker()->dateTimeBetween('-1 year', 'now');
        $updatedAt = (clone $createdAt)->modify(sprintf(
            '+%d days +%d hours +%d minutes +%d seconds',
            random_int(0, 10),
            random_int(0, 23),
            random_int(0, 59),
            random_int(0, 59)
        ));

        $category = CategoryFactory::random();
        $subcategory = self::faker()->randomElement(
            $category->getSubcategory()->toArray()
        );

        return [
            'score' => self::faker()->numberBetween(58499, 3737964),
            'time' => $time,
            'height' => $height,
            'speed' => $height / $time,
            'notes' => self::faker()->text(),
            'is_reviewed' => $isReviewed,
            'status' => $isReviewed
                ? self::faker()->randomElement([
                    Status::APPROVED,
                    Status::REJECTED,
                ])
                : Status::UNREVIEWED,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'media_url' => self::faker()->text(),
            'climber' => UserFactory::random(),
            'category' => $category,
            'subcategory' => $subcategory,
            'verifier' => $isReviewed
                ? UserFactory::random()
                : null,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Climb $climb): void {})
        ;
    }
}
