<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Exception;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @covers \App\Entity\Customer
 */
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

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    public function testUserCanListCustomersFromOwnAccount(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@telefonika.fr");
        $user = $this->createUser("admin@telefonika.fr", "testpwd", $account);
        $jwtToken = $this->getJWTToken($user, $client, "testpwd");
        // create some customers
        for ($i = 0 ; $i < 5 ; $i++) {
            $this->createCustomer("Test","Customer$i", "test$i@email.com", "0$i 00 00 00 00", $account);
        }
        $response = $client->request('GET', '/api/customers', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$jwtToken
            ]
        ]);
        $data = $client->getResponse()->toArray();
        $customersResponse = $data['hydra:member'];

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(5, $customersResponse);
    }

//    public function testUserCannotReadCustomersFromOtherAccounts(): void
//    {
//    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testUserCanAddCustomersOnOwnAccount(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@cdiscount.fr");
        $user = $this->createUser("admin@cdiscount.fr", "testpwd", $account);
        $jwtToken = $this->getJWTToken($user, $client, "testpwd");
        $response = $client->request('POST', '/api/customers', [
            'headers' => [
                'Authorization' => 'Bearer '.$jwtToken
            ],
            'json' => [
              'email' =>  'beyonce.knowles@gmail.com',
              'firstName' => 'Beyoncé',
              'lastName' => 'Knowles',
              'phoneNumber' => '05 62 74 84 71'
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
    }

//    public function testUserCannotAddUsersOnOtherAccounts(): void
//    {
//    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testUserCannotCreateCustomerWithMissingData(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@cdiscount.fr");
        $user = $this->createUser("admin@cdiscount.fr", "testpwd", $account);
        $jwtToken = $this->getJWTToken($user, $client, "testpwd");
        $response = $client->request('POST', '/api/customers', [
            'headers' => [
                'Authorization' => 'Bearer '.$jwtToken
            ],
            'json' => [
                'firstName' => 'Beyoncé',
                'lastName' => 'Knowles',
                'phoneNumber' => '05 62 74 84 71'
            ]
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testUserCannotCreateCustomersWithoutJWT(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@cdiscount.fr");
        $user = $this->createUser("admin@cdiscount.fr", "testpwd", $account);
        $response = $client->request('POST', '/api/customers', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' =>  'beyonce.knowles@gmail.com',
                'firstName' => 'Beyoncé',
                'lastName' => 'Knowles',
                'phoneNumber' => '05 62 74 84 71'
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);
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
