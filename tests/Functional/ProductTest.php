<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Account;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Test\CustomApiTestCase;
use DateTimeImmutable;
use Exception;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @covers \App\Entity\Product
 */
class ProductTest extends CustomApiTestCase
{

    use ReloadDatabaseTrait;

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
        // POST route is forbidden on this API resource
        $this->assertResponseStatusCodeSame(405);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testLoginAndGetJWTToken(): void
    {
        $client = static::createClient();
        $account = $this->createAccount('lionel.richie@darty.fr');
        $user = $this->createUser(
            'francis.nanalle@orange.fr',
            'password',
            $account
        );

        $response = $this->getJWTToken($user, $client);

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function testReadProductsWhileLoggedIn(): void
    {
        $client = static::createClient();
        $account = $this->createAccount('charles.marx@free.fr');
        $testUser = $this->createUser(
            'francis.larbec@msn.com',
            'password',
            $account
        );
        $token = $this->getJWTToken($testUser, $client);

        $response = $client->request('GET', '/api/products',[
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token
                ]
        ]);
        $this->assertNotNull($testUser);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

}
