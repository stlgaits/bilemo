<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CustomerTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

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
    public function testCannotReadCustomersWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/customers');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testUserCanListCustomersFromOwnAccount()
    {

    }

    public function testUserCannotReadCustomersFromOtherAccounts()
    {

    }

    public function testUserCanAddCustomersOnOwnAccount()
    {

    }

    public function testUserCannotAddUsersOnOtherAccounts()
    {

    }

    public function testUserCannotCreateCustomerWithMissingData()
    {

    }

    public function testUserCannotCreateCustomersWithoutJWT()
    {

    }

    public function testUserCanDeleteCustomerOnOwnAccount()
    {

    }

    public function testUserCannotDeleteCustomerOnDifferentAccount()
    {

    }
}
