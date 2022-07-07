<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AccountFixtures extends Fixture
{

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $createdAt = $faker->dateTimeThisDecade();
        for ($i = 0; $i < 15; ++$i) {
            $account = new Account();
            $account->setPrimaryEmail($faker->companyEmail())
                ->setDescription($faker->sentence())
                ->setName($faker->company())
                ->setCreatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s')))
                ->setUpdatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s').'+1 day'))
                ->setIndustry($faker->word())
            ;
            $manager->persist($account);
        }

        $manager->flush();
    }
}