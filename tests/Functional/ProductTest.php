<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Account;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductTest extends ApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testReadProductsWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/products');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testReadOneProductWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/products/1');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCreateProductIsNotAllowed(): void
    {
        $response = static::createClient()->request('POST', '/api/products',[
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [],
        ]);
        // POST route is forbidden on this API
        $this->assertResponseStatusCodeSame(405);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testLoginAndGetJWTToken(): void
    {
        $client = static::createClient();
        $user = $this->createAccountAndUserInDatabase();

        $response = $client->request('POST', '/api/login_check',[
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'testuser8@email.com',
                'password' => 'password'
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function testReadProductsWhileLoggedIn(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('testuser19@email.com');
        $token = $this->getJWTToken($testUser);

        $response = $client->request('GET', '/api/products',[
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token
                ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getJWTToken(User $user): string
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/login_check',[
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $user->getEmail(),
                'password' => 'password'
            ],
        ]);

        $response = json_decode($response->getContent());
        return $response->token;
    }


    /**
     * @throws Exception
     */
    public function createAccountAndUserInDatabase(): User
    {
//        $userRepository = static::getContainer()->get(UserRepository::class);
        $account = new Account();
        $account->setName('Darty');
        $account->setPrimaryEmail('lionel@darty.fr');
        $account->setCreatedAt(new DateTimeImmutable());
        $account->setUpdatedAt(new DateTimeImmutable());

        $user = new User();
        $user->setEmail('testuser19@email.com');
        $user->setFirstName('Kevin');
        $user->setLastName('Weaver');
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setUpdatedAt(new DateTimeImmutable());
        $user->setPassword('$2y$13$2WX2m2dkv.A.tYfBgEJWWupMrxsgj.q6SOHZ/VirwcRapp0.Ra6pG');
        $user->setAccount($account);

        $container = static::getContainer();
        $em = $container->get('doctrine')->getManager();
//        $em = self::$container->get(EntityManagerInterface::class);
        $em->persist($account);
        $em->persist($user);
        $em->flush();
        return $user;
    }
}
