<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Domain\Entity\Product;
use App\Application\Service\DiscountService;
use PHPUnit\Framework\TestCase;

class DiscountServiceTest extends TestCase
{
    private DiscountService $discountService;

    protected function setUp(): void
    {
        $this->discountService = new DiscountService();
    }

    public function testApplyDiscountsWithBootsCategory(): void
    {
        $product = new Product();
        $product->setSku('000001')
               ->setName('BV Lean leather ankle boots')
               ->setCategory('boots')
               ->setPrice(89000);

        $result = $this->discountService->applyDiscounts($product);

        $this->assertEquals('000001', $result->sku);
        $this->assertEquals('BV Lean leather ankle boots', $result->name);
        $this->assertEquals('boots', $result->category);
        $this->assertEquals(89000, $result->price->original);
        $this->assertEquals(62300, $result->price->final);
        $this->assertEquals('30%', $result->price->discount_percentage);
        $this->assertEquals('EUR', $result->price->currency);
    }

    public function testApplyDiscountsWithSpecialSku(): void
    {
        $product = new Product();
        $product->setSku('000003')
               ->setName('Ashlington leather ankle boots')
               ->setCategory('boots')
               ->setPrice(71000);

        $result = $this->discountService->applyDiscounts($product);

        // When multiple discounts collide, the bigger discount must be applied (30% > 15%)
        $this->assertEquals(71000, $result->price->original);
        $this->assertEquals(49700, $result->price->final);
        $this->assertEquals('30%', $result->price->discount_percentage);
        $this->assertEquals('EUR', $result->price->currency);
    }

    public function testApplyDiscountsWithNoDiscount(): void
    {
        $product = new Product();
        $product->setSku('000004')
               ->setName('Naima embellished suede sandals')
               ->setCategory('sandals')
               ->setPrice(79500);

        $result = $this->discountService->applyDiscounts($product);

        $this->assertEquals(79500, $result->price->original);
        $this->assertEquals(79500, $result->price->final);
        $this->assertNull($result->price->discount_percentage);
        $this->assertEquals('EUR', $result->price->currency);
    }

    public function testApplyDiscountsWithOnlySkuDiscount(): void
    {
        $product = new Product();
        $product->setSku('000003')
               ->setName('Special Product')
               ->setCategory('sneakers')
               ->setPrice(100000);

        $result = $this->discountService->applyDiscounts($product);

        $this->assertEquals(100000, $result->price->original);
        $this->assertEquals(85000, $result->price->final);
        $this->assertEquals('15%', $result->price->discount_percentage);
        $this->assertEquals('EUR', $result->price->currency);
    }
}