<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Exception;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CustomerTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

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

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testUserCannotDeleteCustomerWithoutAuth()
    {
        $client = self::createClient();
        $container = static::getContainer();
        $account = $this->createAccount('charles.laborde@fnac.com');
        $user = $this->createUser('louise.vandenbeck@gmail.com', 'banana', $account);
        $customer = $this->createCustomer();
        $response = $client->request('DELETE', '/api/customers/'.$customer->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}
