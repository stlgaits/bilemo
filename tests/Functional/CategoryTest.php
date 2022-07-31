<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Test\CustomApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @covers \App\Entity\Category
 */
class CategoryTest extends CustomApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testReadCategoriesWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/categories');
        $this->assertResponseStatusCodeSame(401);
    }
}
