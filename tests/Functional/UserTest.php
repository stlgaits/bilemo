<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Account;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @covers \App\Entity\User
 */
class UserTest extends CustomApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testReadUsersWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCreateUserViaPostRequestWithoutJWT()
    {
        $container = static::getContainer();
        $user = new User();
        $user->setEmail('joe.cook@gmail.com');
        $password = 'porridge';
        $encoded = $container->get('security.password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setFirstName('Joe');
        $user->setLastName('Cook');
        $response = static::createClient()->request('POST', '/api/users', [
            'json' => [
                'email' => $user->getEmail(),
                'password' =>   $user->getPassword(),
                'firstName' =>  $user->getFirstName(),
                'lastName' =>   $user->getLastName()
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(["message" => "JWT Token not found"]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testCreateUserViaPostRequestWithJWTAuth()
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn($client,
            "already.register@email.com",
            "choucroute",
            "test@company.com"
        );
        $user = new User();
        $user->setEmail('joe.cook@gmail.com');
        $password = 'porridge';
        $encoded = $container->get('security.password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setFirstName('Joe');
        $user->setLastName('Cook');
        $em = $this->getEntityManager();
        $accountRepository = $em->getRepository(Account::class);
        $account = $accountRepository->findOneBy(['primaryEmail' =>  "test@company.com"]);
        // set the newly-created user's account to the currently logged-in user account
        // since we should only allow a user to assign a new user to their OWN account
        $user->setAccount($account);
        $accountIri = $this->findIriBy(Account::class, ['primaryEmail' => 'test@company.com']);
        $response = static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ],
            'json' => [
                'email' => $user->getEmail(),
                'password' =>   $user->getPassword(),
                'firstName' =>  $user->getFirstName(),
                'lastName' =>   $user->getLastName(),
                'account' => $accountIri
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
    }
}
