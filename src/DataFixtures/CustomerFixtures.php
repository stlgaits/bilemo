<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Customer;
use App\Repository\AccountRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    private ContainerBagInterface $params;
    private AccountRepository $accountRepository;
    /**
     * @var Account[]
     */
    private array $accounts;
    private \Faker\Generator $faker;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->accounts = $this->accountRepository->findAll();
        $this->faker = Factory::create('fr_FR');
    }

    private static function getReferenceKey(int $key): string
    {
        return sprintf('customer_%s', $key);
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $createdAt = $this->faker->dateTimeThisDecade();
        for ($i = 0; $i < 25; ++$i) {
            $customer = new Customer();
            $customer->setEmail($this->faker->email())
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setAccount($this->faker->randomElement($this->accounts))
                ->setPhoneNumber($this->faker->phoneNumber())
                ->setCreatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s')))
                ->setUpdatedAt(new DateTimeImmutable($createdAt->format('Y-m-d H:i:s').'+1 day'))

            ;
            $this->addReference(self::getReferenceKey($i), $customer);
            $manager->persist($customer);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AccountFixtures::class];
    }
}
