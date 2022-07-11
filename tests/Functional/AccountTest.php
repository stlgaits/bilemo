<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AccountTest extends ApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testReadAccountsCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/accounts');
        // The list of accounts should not be available
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testReadOneAccountWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/accounts/1');
        // The list of accounts should not be available
        $this->assertResponseStatusCodeSame(401);
    }
}
