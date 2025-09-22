<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    public function testGetProductsEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('pagination', $content);
    }

    public function testGetProductsWithCategoryFilter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products?category=boots');

        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $products = $content['data'];
        
        // Verify all returned products are in boots category
        foreach ($products as $product) {
            $this->assertEquals('boots', $product['category']);
            // Verify boots have 30% discount applied
            $this->assertEquals('30%', $product['price']['discount_percentage']);
            $this->assertEquals('EUR', $product['price']['currency']);
        }
    }

    public function testGetProductsWithPriceLessThanFilter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products?priceLessThan=80000');

        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $products = $content['data'];
        
        // Verify all returned products have original price <= 80000 (before discounts)
        foreach ($products as $product) {
            $this->assertLessThanOrEqual(80000, $product['price']['original']);
        }
    }

    public function testDiscountCollisionHandling(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products?category=boots');

        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $products = $content['data'];
        
        // Find product with SKU 000003 (should have both boots 30% and SKU 15% discounts)
        $product000003 = null;
        foreach ($products as $product) {
            if ($product['sku'] === '000003') {
                $product000003 = $product;
                break;
            }
        }
        
        if ($product000003) {
            // Should apply 30% discount (bigger than 15%)
            $this->assertEquals('30%', $product000003['price']['discount_percentage']);
            $this->assertEquals(71000, $product000003['price']['original']);
            $this->assertEquals(49700, $product000003['price']['final']);
        }
    }

    public function testMaximum5ElementsReturned(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $this->assertLessThanOrEqual(5, count($content['data']));
    }

    public function testGetProductsWithInvalidPriceLessThan(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products?priceLessThan=invalid');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $content);
    }

    public function testProductResponseStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('pagination', $content);
        
        if (!empty($content['data'])) {
            $product = $content['data'][0];
            
            $this->assertArrayHasKey('sku', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('category', $product);
            $this->assertArrayHasKey('price', $product);
            
            $price = $product['price'];
            $this->assertArrayHasKey('original', $price);
            $this->assertArrayHasKey('final', $price);
            $this->assertArrayHasKey('discount_percentage', $price);
            $this->assertArrayHasKey('currency', $price);
            $this->assertEquals('EUR', $price['currency']);
        }
    }

    public function testPaginationStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products?page=1&itemsPerPage=2');

        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('pagination', $content);
        $pagination = $content['pagination'];
        
        $this->assertArrayHasKey('current_page', $pagination);
        $this->assertArrayHasKey('items_per_page', $pagination);
        $this->assertArrayHasKey('total_items', $pagination);
        $this->assertArrayHasKey('total_pages', $pagination);
        
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(2, $pagination['items_per_page']);
    }
}