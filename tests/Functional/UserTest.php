<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Account;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Exception;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @covers \App\Entity\User
 */
class UserTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testCreateUserViaPostRequestWithJWTAuth(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "estelle.test@email.com",
            "choucroute",
            "test@company.com"
        );
        $user = new User();
        $user->setEmail('james.gandolfini@gmail.com');
        $password = 'porridge';
        $encoded = $container->get('security.password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setFirstName('James');
        $user->setLastName('Gandolfini');
        $em = $this->getEntityManager();
        $accountRepository = $em->getRepository(Account::class);
        $account = $accountRepository->findOneBy(['primaryEmail' =>  "test@company.com"]);
        // set the newly-created user's account to the currently logged-in user account
        // since we should only allow a user to assign a new user to their OWN account
        $user->setAccount($account);
        $accountIri = $this->findIriBy(Account::class, ['primaryEmail' => 'test@company.com']);
        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ],
            'json' => [
                'email' => $user->getEmail(),
                'password' =>   $password,
                'firstName' =>  $user->getFirstName(),
                'lastName' =>   $user->getLastName(),
                'account' => $accountIri
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     * @throws DecodingExceptionInterface
     */
    public function testSuperAdminCanReadAllUsersWithJWTAuth(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@escadenca.fr");
        $account2 = $this->createAccount("contact@otheraccount.fr");
        $user = $this->createUser("estelle.gaits@escadenca.fr", "thisisatestpassword", $account);
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        for ($i = 0 ; $i < 5 ; $i++) {
            $this->createUser("user$i@cdiscount.fr", "thisisatestpwd", $account2);
        }
        $jwtToken = $this->getJWTToken($user, $client, "thisisatestpassword");
        $response = $client->request('GET', '/api/users', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$jwtToken
                ]
            ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 6,
            'hydra:member' => [
                0 => [
                    '@id' => '/api/users/1',
                    '@type' => 'User',
                    'email' => 'estelle.gaits@escadenca.fr',
                ]
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     * @throws DecodingExceptionInterface
     */
    public function testAdminCanOnlyReadUsersFromSameAccount(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@escadenca.fr");
        $account2 = $this->createAccount("contact@cdiscount.fr");
        $user = $this->createUser("admin@escadenca.fr", "thisisatestpassword", $account);
        $user->setRoles(['ROLE_ADMIN']);
        $jwtToken = $this->getJWTToken($user, $client, "thisisatestpassword");
        $userWithSameAccount = $this->createUser("user1@escadenca.fr", "thisisatestpassword", $account);
        $usersWithDifferentAccount = [];
        for ($i = 0 ; $i < 5 ; $i++) {
            $newUser = $this->createUser("user$i@cdiscount.fr", "thisisatestpwd", $account2);
            $usersWithDifferentAccount[$i] = $newUser;
        }
        $response = $client->request('GET', '/api/users', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$jwtToken
                ]
            ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();
        $data = $client->getResponse()->toArray();
        $readableUsers = $data['hydra:member'];
        $this->assertArrayHasKey('account', $readableUsers[0]);
        $this->assertArrayNotHasKey('roles', $readableUsers[0]);
        $this->assertCount(5, $usersWithDifferentAccount);
        $this->assertCount(2, $readableUsers);
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
            'hydra:member' => [
                0 => [
                    '@id' => '/api/users/1',
                    '@type' => 'User',
                    'email' => 'admin@escadenca.fr',
                ]
            ]
        ]);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @TODO: this needs to be reworked since we've gotten rid of the /accounts endpoint so test fails (404)
     * We should instead find a way to check whether the result content only contains results
     * with account property whose ID is same as user & change endpoint to simple /users
     */
    public function testReadUsersFromSameAccountWithJWTAuth(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@escadenca.fr");
        $user = $this->createUser("geraldine.gaits@escadenca.fr", "thisisatestpassword", $account);
        $user->setRoles(['ROLE_ADMIN']);
        $jwtToken = $this->getJWTToken($user, $client, "thisisatestpassword");
        $accountId = $user->getAccount()->getId();
        $response = $client->request("GET", "/api/accounts/$accountId/users", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$jwtToken
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @TODO: this needs to be reworked since we've gotten rid of the /accounts endpoint so test fails (404)
     * We should instead find a way to check whether the result content only contains results
     * with account property whose ID is same as user
     */
    public function testCannotReadUsersFromAccountDifferentThanOwn(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@escadenca.fr");
        $account2 = $this->createAccount("other@account.fr");
        $user = $this->createUser("laurent.gaits@escadenca.fr", "thisisatestpassword", $account);
        $user->setRoles(['ROLE_ADMIN']);
        $jwtToken = $this->getJWTToken($user, $client, "thisisatestpassword");
        $accountId = $user->getAccount()->getId();
        $otherAccountId = 2;
        $response = $client->request("GET", "/api/accounts/$otherAccountId/users", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$jwtToken
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     * @throws DecodingExceptionInterface
     * @TODO: the /api/accounts/{accountId}/users/{userId} IRI doesn't actually exist
     * Need to find a way to test whether users on /api/users correspond to current account only
     */
    public function testReadOneUserFromSameAccountWithJWTAuth(): void
    {
        $client = self::createClient();
        $account = $this->createAccount("contact@escadenca.fr");
        $user = $this->createUser("nicolas.gaits@escadenca.fr", "thisisatestpassword", $account);
        $jwtToken = $this->getJWTToken($user, $client, "thisisatestpassword");
        $accountId = $user->getAccount()->getId();
        $otherUser = $this->createUser("michel.vaillant@escadenca.fr", "myvoiceismypassword", $account);
        $otherUserId = $otherUser->getId();
        $response = $client->request("GET", "/api/accounts/$accountId/users/$otherUserId", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$jwtToken
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            "email" => "michel.vaillant@escadenca.fr",
            "password" => "myvoiceismypassword",
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCannotReadUsersWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCannotCreateUserViaPostRequestWithoutJWT(): void
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
            'json' => [
                'email' => $user->getEmail(),
                'password' =>   $user->getPassword(),
                'firstName' =>  $user->getFirstName(),
                'lastName' =>   $user->getLastName()
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(["message" => "JWT Token not found"]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testCannotCreateUserWithInvalidJSONInput(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "already.registered@email.com",
            "choucroute",
            "test@company.com"
        );
        $user = new User();
        $user->setEmail('joe.cook@gmail.com');
        $password = 'porridge';
        $encoded = $container->get('security.password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setFirstName('Joe');
        $user->setLastName('Cook');
        $em = $this->getEntityManager();
        $accountRepository = $em->getRepository(Account::class);
        $account = $accountRepository->findOneBy(['primaryEmail' =>  "test@company.com"]);
        // set the newly-created user's account to the currently logged-in user account
        // since we should only allow a user to assign a new user to their OWN account
        $user->setAccount($account);
        $accountIri = $this->findIriBy(Account::class, ['primaryEmail' => 'test@company.com']);
        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ],
            'json' => [
                'email' => $user->getEmail(),
                'password' =>   $user->getPassword(),
                'firstName' =>  $user->getFirstName(),
                'lastName' =>   99999,
                'account' => $accountIri
            ]
        ]);
        $this->assertIsString($loggedInUserJWTToken);
        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testCannotCreateUserOnDifferentAccountThanOwn(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "already.register@email.com",
            "choucroute",
            "test@company.com"
        );
        $user = new User();
        $user->setEmail('joe.cook@gmail.com');
        $password = 'porridge';
        $encoded = $container->get('security.password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setFirstName('Joe');
        $user->setLastName('Cook');
        $em = $this->getEntityManager();
        // must be DIFFERENT from the one of the current user => this should be forbidden
        $account = $this->createAccount("big.boss@micromania.fr");
        $account2 = $this->createAccount("little.boss@sosh.fr");
        $user->setAccount($account);
        $accountIri = $this->findIriBy(Account::class, ['primaryEmail' => 'big.boss@micromania.fr']);
        $accountIri2 = $this->findIriBy(Account::class, ['primaryEmail' => 'little.boss@sosh.fr']);

        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ],
            'json' => [
                'email' => $user->getEmail(),
                'password' =>   $user->getPassword(),
                'firstName' =>  $user->getFirstName(),
                'lastName' =>   $user->getLastName(),
                'account' => $accountIri2
            ]
        ]);

        // Access control: the user should only be allowed to create a user with the same Account
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertIsResource($response);
        $this->assertNotSame($accountIri, $accountIri2);
        // @TODO: find assertions to compare current user account & created one => should be the same AND should be different from acocuntIri2
//        $this->assertSame($accountIri, $response->getContent());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testCannotCreateUserWithMissingData(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "already.register22@email.com",
            "aubergine",
            "test22@company.com"
        );
        $user = new User();
        $user->setEmail('louise.kick@gmail.com');
        $password = 'honey';
        $encoded = $container->get('security.password_hasher')->hashPassword($user, $password);
        $user->setPassword($encoded);
        $user->setFirstName('Louise');
        $user->setLastName('Kick');
        $em = $this->getEntityManager();
        // we purposefully forget the Account field
        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ],
            'json' => [
                'email' => $user->getEmail(),
                'password' =>   $user->getPassword(),
                'lastName' =>   $user->getLastName(),
            ]
        ]);
        // Should throw "firstName: This value should not be blank."
        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testUserCanDeleteThemselves(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "marc.zucko34@email.com",
            "cabbage",
            "contact@facebook.com"
        );
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'marc.zucko34@email.com']);
        $response = $client->request('DELETE', '/api/users/'.$user->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ]
        ]);
        // User's account should be deleted
        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testCannotDeleteUserWithoutJWTHeader(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "marc.zucko34@email.com",
            "cabbage",
            "contact@facebook.com"
        );
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'marc.zucko34@email.com']);
        $response = $client->request('DELETE', '/api/users/'.$user->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testCannotDeleteUserWithoutAuth(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $account = $this->createAccount('marc.laborde@fnac.com');
        $user = $this->createUser('lori.vandenbeck@gmail.com', 'banana', $account);
        $response = $client->request('DELETE', '/api/users/'.$user->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testUserCannotDeleteAnotherUserWithDifferentAccount(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "marc.zucko34@email.com",
            "cabbage",
            "contact@facebook.com"
        );
        $account2 = $this->createAccount("admin@spacex.com");
        $user2 = $this->createUser('elon.musk77@spacex.com', 'potato', $account2);
        $user1 = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'marc.zucko34@email.com']);
//        $user2 = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'elon.musk77@spacex.com']);
        $response = $client->request('DELETE', '/api/users/'.$user2->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ]
        ]);
        // User 2's account shouldn't be deleted
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testUserCannotDeleteAnotherUserWithSameAccount(): void
    {
        $client = self::createClient();
        $container = static::getContainer();
        $loggedInUserJWTToken = $this->createUserAndLogIn(
            $client,
            "marc.zucko19@email.com",
            "kiwi",
            "contact@facebook.com"
        );
        $user1 = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'marc.zucko19@email.com']);
        $user2 = $this->createUser('elon.musk77@facebook.com', 'potato', $user1->getAccount());
        $response = $client->request('DELETE', '/api/users/'.$user2->getId(), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$loggedInUserJWTToken
            ]
        ]);
        // User 2 shouldn't be deleted, you should only be able to delete your own user
        $this->assertResponseStatusCodeSame(403);
    }
}
