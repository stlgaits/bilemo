<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/']);
    }
}
