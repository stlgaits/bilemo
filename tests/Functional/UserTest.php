<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @covers \App\Entity\User
 */
class UserTest extends ApiTestCase
{
//    public function testSomething(): void
//    {
//        $response = static::createClient()->request('GET', '/');
//
//        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['@id' => '/']);
//    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testReadUsersWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);
    }
}
