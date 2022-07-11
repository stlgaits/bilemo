<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\User;
use App\Repository\AccountRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    protected UserPasswordHasherInterface $hasher;
    private ContainerBagInterface $params;
    private AccountRepository $accountRepository;
    /**
     * @var Account[]
     */
    private array $accounts;
    private \Faker\Generator $faker;

    public function __construct(UserPasswordHasherInterface $hasher, ContainerBagInterface $params, AccountRepository $accountRepository)
    {
        $this->hasher = $hasher;
        $this->params = $params;
        $this->accountRepository = $accountRepository;
        $this->accounts = $this->accountRepository->findAll();
        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $createdAt = $this->faker->dateTimeThisDecade();
        for ($i = 0; $i < 30; ++$i) {
            $user = new User();
            $password = $this->faker->word();
            $user->setEmail($this->faker->email())
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setPassword($this->hasher->hashPassword($user, $password))
                ->setAccount($this->faker->randomElement($this->accounts))
                ->setCreatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s')))
                ->setUpdatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s').'+1 day'))

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
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function addSuperAdmin(): User
    {
        $admin = new User();
        $admin->setEmail($this->params->get('admin_email_address'))
            ->setFirstName($this->params->get('admin_firstname'))
            ->setLastName($this->params->get('admin_lastname'))
            ->setPassword($this->hasher->hashPassword($admin, $this->params->get('admin_password')))
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setUpdatedAt(new DateTimeImmutable('now'))
            ->setAccount($this->faker->randomElement($this->accounts))
            ->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);

        return $admin;
    }

    public static function getReferenceKey($key): string
    {
        return sprintf('user_%s', $key);
    }

    public function getDependencies(): array
    {
        return [AccountFixtures::class];
    }
}
