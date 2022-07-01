<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AccountFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 15; ++$i) {
            $account = new Account();
            $account->setPrimaryEmail($faker->companyEmail())
                ->setDescription($faker->sentence())
                ->setName($faker->company())
                ->setIndustry($faker->word())
            ;
            $this->addReference(self::getReferenceKey($i), $user);
            $manager->persist($user);
        }

        // Create Super Admin User
        $admin = $this->addSuperAdmin();
        $manager->persist($admin);

        $manager->flush();
    }
}