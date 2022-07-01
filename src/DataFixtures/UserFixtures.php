<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\AccountRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    protected UserPasswordHasherInterface $hasher;
    private ContainerBagInterface $params;
    private AccountRepository $accountRepository;

    public function __construct(UserPasswordHasherInterface $hasher, ContainerBagInterface $params, AccountRepository $accountRepository)
    {
        $this->hasher = $hasher;
        $this->params = $params;
        $this->accountRepository = $accountRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $accounts = $this->accountRepository->findAll();
        for ($i = 0; $i < 30; ++$i) {
            $user = new User();
            $password = $faker->word();
            $user->setEmail($faker->email())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPassword($this->hasher->hashPassword($user, $password))
                ->setAccount($faker->randomElement($accounts))

            ;
            $this->addReference(self::getReferenceKey($i), $user);
            $manager->persist($user);
        }

        // Create Super Admin User
        $admin = $this->addSuperAdmin();
        $manager->persist($admin);

        $manager->flush();
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function addSuperAdmin(): User
    {
        $admin = new User();
        $admin->setEmail($this->params->get('admin_email_address'))
            ->setFirstName($this->params->get('admin_username'))
            ->setLastName($this->params->get('admin_username'))
            ->setPassword($this->hasher->hashPassword($admin, $this->params->get('admin_password')))
            ->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);

        return $admin;
    }

    public static function getReferenceKey($key)
    {
        return sprintf('user_%s', $key);
    }

    public function getDependencies(): array
    {
        return [AccountFixtures::class];
    }
}