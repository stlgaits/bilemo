<?php

namespace App\DataFixtures;

use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0 ; $i < 75 ; $i++) {
            $category = $this->getReference(CategoryFixtures::getReferenceKey($i % 7));
            $product = new Product();
            $product->setName(ucfirst($faker->word()))
                ->setPrice($faker->randomFloat(2, 8, 5000))
                ->setBrand($faker->company())
                ->setSku($faker->ean13())
                ->setDescription($faker->text())
                ->setCreatedAt(new DateTimeImmutable('-1 day'))
                ->setUpdatedAt(new DateTimeImmutable())
                ->setAvailable($faker->boolean(75))
                ->setCategory($category)
            ;
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
