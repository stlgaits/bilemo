<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    private array $categories = [
        'smartphone',
        'smartwatch',
        'tv',
        'accessories',
        'smarthome',
        'refurbished',
        'hifi'
    ];

    public static function getReferenceKey($key): string
    {
        return sprintf('category_%s', $key);
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $createdAt = $faker->dateTimeThisDecade();
        foreach ($this->categories as $key => $categoryName) {
            $category = new Category();
            $category->setName(ucfirst($categoryName))
                    ->setCreatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s')))
                ->setUpdatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s').'+5 day'))
            ;
            $manager->persist($category);
            $this->addReference(self::getReferenceKey($key), $category);
        }

        $manager->flush();
    }
}
