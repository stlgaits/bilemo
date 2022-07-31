<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Test\CustomApiTestCase;
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
     * @throws TransportExceptionInterface
     */
    public function testCreateUserViaPostRequest()
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
             'email' => $user->getEmail(),
              'password' =>   $user->getPassword(),
              'firstName' =>  $user->getFirstName(),
              'lastName' =>   $user->getLastName()
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}
