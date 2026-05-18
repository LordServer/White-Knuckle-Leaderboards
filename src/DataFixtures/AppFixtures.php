<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\RankMethod;
use App\Entity\Subcategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Ascending');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Score');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Score Ascending');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Time');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Time Ascending');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Height');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Height Ascending');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Speed');
//        $manager->persist($rankMethod);
//
//        $rankMethod = new RankMethod();
//        $rankMethod->setName('Speed Ascending');
//        $manager->persist($rankMethod);

//        $subcategory = new Subcategory();
//        $subcategory->setName('Normal');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Hardmode');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Iron Knuckle');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Hardmode Iron Knuckle');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Demo');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Advanced Course');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Fractured Territory');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Roach Run');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Comms Array');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Shuttered Rift');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Parasite');
//        $manager->persist($subcategory);
//
//        $subcategory = new Subcategory();
//        $subcategory->setName('Parasite Hardmode');
//        $manager->persist($subcategory);
//
//        $category = new Category();
//        $category->setName('Campaign');
//        $category->setRules('Complete the game.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 5));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 5));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Endless Substructure');
//        $category->setRules('Climb, but in Substructure.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Endless Underworks');
//        $category->setRules('Climb, but in Underworks.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Endless Silos');
//        $category->setRules('Climb, but in Silos.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Endless Pipeworks');
//        $category->setRules('Climb, but in Pipeworks.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Endless Habitation');
//        $category->setRules('Climb, but in Habitation.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Endless Abyss');
//        $category->setRules('Climb, but in Abyss.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Endless Superstructure');
//        $category->setRules('Climb, but in Superstructure.');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 3));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 4));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Challenge Maps - Time');
//        $category->setRules('Challenge Maps - Time');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 5));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 6));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 7));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 8));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 9));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 10));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 11));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 12));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Challenge Maps - Score');
//        $category->setRules('Challenge Maps - Score');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 2));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 8));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 11));
//        $manager->persist($category);
//
//        $category = new Category();
//        $category->setName('Tutorial %');
//        $category->setRules('Tutorial %');
//        $category->setIsArchived(false);
//        $category->setRankMethod($manager->getReference(RankMethod::class, 5));
//        $category->addSubcategory($manager->getReference(Subcategory::class, 1));
//        $manager->persist($category);
//
//        $manager->flush();
    }
}
