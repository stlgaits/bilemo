<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Account;
use App\Test\CustomApiTestCase;
use Exception;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @covers \App\Entity\Account
 */
class AccountTest extends CustomApiTestCase
{

    use ReloadDatabaseTrait;

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

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     * @throws DecodingExceptionInterface
     * A user should be able to access their own Account (but no other)
     */
    public function testReadUsersOwnAccountWithJWTAuthToken(): void
    {
        $client = self::createClient();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "sophie.rodriguez22@email.com",
            "broccoli",
            "contact@orange.com"
        );
        $account1= $this->getEntityManager()->getRepository(Account::class)->findOneBy(['primaryEmail' => 'contact@orange.com']);
        $account2 = $this->createAccount('contact@sfr.com');

        $response = $client->request('GET', '/api/accounts/'.$account1->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '. $loggedInUserJWTToken
            ]
        ]);

        // Only the current user's account should be readable
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['primaryEmail' => 'contact@orange.com']);
        $data = $client->getResponse()->toArray();
        $this->assertArrayHasKey("industry", $data);
        $this->assertArrayHasKey("description", $data);
        $this->assertArrayHasKey("name", $data);
        $this->assertArrayHasKey("users", $data);
        $this->assertArrayHasKey("createdAt", $data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     * A user shouldn't be able to access Accounts other than their own
     */
    public function testReadOneAccountWithJWTAuthToken(): void
    {
        $client = self::createClient();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "emily.sanchez78@email.com",
            "cauliflower",
            "contact@orange.com"
        );

        $account2 = $this->createAccount('contact@sfr.com');
        $account2Id = $account2->getId();

        $response = $client->request('GET', '/api/accounts/'.$account2Id, [
            'headers' => [
                'Authorization' => 'Bearer '. $loggedInUserJWTToken
            ]
        ]);

        // Only the current user's account should be readable
        $this->assertResponseStatusCodeSame(403);
    }
}
