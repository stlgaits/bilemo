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

    public function testUserCanListCustomersFromOwnAccount(): void
    {
    }

    public function testUserCannotReadCustomersFromOtherAccounts(): void
    {
    }

    public function testUserCanAddCustomersOnOwnAccount(): void
    {
    }

    public function testUserCannotAddUsersOnOtherAccounts(): void
    {
    }

    public function testUserCannotCreateCustomerWithMissingData(): void
    {
    }

    public function testUserCannotCreateCustomersWithoutJWT(): void
    {
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function testUserCanDeleteCustomerOnOwnAccount(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $account = $this->createAccount('marie.dupont@fnac.com');
        $user = $this->createUser('vincent.rock@gmail.com', 'camomille', $account);
        $customer = $this->createCustomer(
            'Elodie',
            'Lacour',
            'elodie.lacour@gmail.com',
            '06 02 04 04 03',
            $account
        );
        $token = $this->getJWTToken($user, $client, 'camomille');
        $response = $client->request('DELETE', '/api/customers/'.$customer->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testUserCannotDeleteCustomerOnDifferentAccount(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $userAccount = $this->createAccount('charles.laborde@fnac.com');
        $customerAccount = $this->createAccount('remi.lafont@cdiscount.com');
        $user = $this->createUser('louise.vandenbeck@gmail.com', 'banana', $userAccount);
        $token = $this->getJWTToken($user, $client, 'banana');
        $customer = $this->createCustomer(
            'Carlie',
            'Jensen',
            'carlie.jensen@gmail.com',
            '07 07 15 04 03',
            $customerAccount
        );
        $response = $client->request('DELETE', '/api/customers/'.$customer->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '. $token
            ]
        ]);

        // Resource should be entirely unacessible for the User, hence resulting in a 404
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testUserCannotDeleteCustomerWithoutAuth(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $account = $this->createAccount('charles.laborde@fnac.com');
        $user = $this->createUser('louise.vandenbeck@gmail.com', 'banana', $account);
        $customer = $this->createCustomer(
            'Kévin',
            'Leblanc',
            'kevin.leblanc@gmail.com',
            '07 06 05 04 03',
            $account
        );
        $response = $client->request('DELETE', '/api/customers/'.$customer->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}
