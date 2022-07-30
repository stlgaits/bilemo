<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Account;
use App\Entity\User;
use DateTimeImmutable;
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
        $em = $container->get('doctrine')->getManager();
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
        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();
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
     * @coversNothing
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getJWTToken(User $user, Client $client): string
    {
        $response = $client->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $user->getEmail(),
                'password' => 'password'
            ],
        ]);

        $response = json_decode($response->getContent());
        $this->assertNotNull($response->token);
        return $response->token;
    }

//    /**
//     * @throws TransportExceptionInterface
//     */
//    public function logIn(Client $client, string $email, string $password)
//    {
//        $client->request('POST', '/api/login_check',[
//            'headers' => ['Content-Type' => 'application/json'],
//            'json' => [
//                'username' => $email,
//                'password' => $password
//            ],
//        ]);
//
//        $this->assertResponseStatusCodeSame(204);
//    }

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
        return $this->getJWTToken($user, $client);
    }
}
