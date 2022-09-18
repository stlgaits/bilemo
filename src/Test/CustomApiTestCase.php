<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Account;
use App\Entity\Customer;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CustomApiTestCase extends ApiTestCase
{
    /**
     * @throws Exception
     */
    public function createUser(string $email, string $password, Account $account): User
    {
        $container = static::getContainer();
        $em = $this->getEntityManager();
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName(substr($email, 0, strpos($email, '.')));
        $lastNameOffset =  strpos($email, '.');
        $user->setLastName(substr($email, $lastNameOffset, strpos($email, '@', $lastNameOffset)));
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setUpdatedAt(new DateTimeImmutable());
        $encoded = $container->get('security.password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setAccount($account);
        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * @throws Exception
     */
    public function createAccount(string $primaryEmail): Account
    {
        $em = $this->getEntityManager();
        $account = new Account();
        $account->setName(substr($primaryEmail, 0, strpos($primaryEmail, '@')));
        $account->setPrimaryEmail($primaryEmail);
        $account->setCreatedAt(new DateTimeImmutable());
        $account->setUpdatedAt(new DateTimeImmutable());
        $em->persist($account);
        $em->flush();
        return $account;
    }

    /**
     * @throws Exception
     */
    public function createCustomer(string $firstName, string $lastName, string $email, string $phoneNumber, Account $account): Customer
    {
        $em = $this->getEntityManager();
        $customer = new Customer();
        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);
        $customer->setPhoneNumber($phoneNumber);
        $customer->setAccount($account);
        $customer->setCreatedAt(new DateTimeImmutable());
        $customer->setUpdatedAt(new DateTimeImmutable());

        $em->persist($customer);
        $em->flush();
        return $customer;
    }
    /**
     * @coversNothing
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getJWTToken(User $user, Client $client, string $plainPassword): string
    {
        $response = $client->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $user->getEmail(),
                'password' => $plainPassword
            ],
        ]);
        $responseArray = json_decode($response->getContent());
        $token = $responseArray->token;
        $this->assertNotNull($token);
        return $token;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * Undecided yet whether I want to use this logic because it only returns the token & not the User anymore
     */
    protected function createUserAndLogIn(Client $client, string $email, string $password, string $primaryEmail): string
    {
        $account = $this->createAccount($primaryEmail);
        $user = $this->createUser($email, $password, $account);
        $token =  $this->getJWTToken($user, $client, $password);
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Account::class, $account);
        $this->assertNotNull($account);
        $this->assertNotNull($token);
        $this->assertIsString($token);
        return $token;
    }

    /**
     * @throws Exception
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }
}
