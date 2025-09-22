<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Application\Service\DiscountServiceInterface;
use App\Application\Service\ProductService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class ProductServiceTest extends TestCase
{
    private ProductService $productService;
    private ProductRepositoryInterface|MockObject $productRepository;
    private DiscountServiceInterface|MockObject $discountService;
    private CacheItemPoolInterface|MockObject $cache;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->discountService = $this->createMock(DiscountServiceInterface::class);
        $this->cache = $this->createMock(CacheItemPoolInterface::class);

        $this->productService = new ProductService(
            $this->productRepository,
            $this->discountService,
            $this->cache
        );
    }

    public function testGetProductsWithDiscountsFromCache(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())
                  ->method('isHit')
                  ->willReturn(true);
        
        $expectedResult = [];
        $cacheItem->expects($this->once())
                  ->method('get')
                  ->willReturn($expectedResult);

        $this->cache->expects($this->once())
                   ->method('getItem')
                   ->willReturn($cacheItem);

        $result = $this->productService->getProductsWithDiscounts();

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetProductsWithDiscountsFromDatabase(): void
    {
        $product = new Product();
        $product->setSku('000001')
               ->setName('Test Product')
               ->setCategory('boots')
               ->setPrice(89000);

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())
                  ->method('isHit')
                  ->willReturn(false);

        $this->cache->expects($this->once())
                   ->method('getItem')
                   ->willReturn($cacheItem);

        $this->productRepository->expects($this->once())
                               ->method('findWithFilters')
                               ->with(null, null, 5, 0)
                               ->willReturn([$product]);

        $this->discountService->expects($this->once())
                             ->method('applyDiscounts')
                             ->with($product);

        $cacheItem->expects($this->once())
                  ->method('set');
        $cacheItem->expects($this->once())
                  ->method('expiresAfter')
                  ->with(300);

        $this->cache->expects($this->once())
                   ->method('save')
                   ->with($cacheItem);

        $this->productService->getProductsWithDiscounts();
    }

    public function testGetTotalCount(): void
    {
        $expectedCount = 10;

        $this->productRepository->expects($this->once())
                               ->method('countWithFilters')
                               ->with('boots', 50000)
                               ->willReturn($expectedCount);

        $result = $this->productService->getTotalCount('boots', 50000);

        $this->assertEquals($expectedCount, $result);
    }
}