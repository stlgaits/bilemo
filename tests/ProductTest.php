<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductTest extends ApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testReadProductsWithNoJWTToken(): void
    {
        $response = static::createClient()->request('GET', '/api/products');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCreateProductIsNotAllowed(): void
    {
        $response = static::createClient()->request('POST', '/api/products',[
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [],
        ]);
        // POST route is forbidden on this API
        $this->assertResponseStatusCodeSame(405);
    }
}
