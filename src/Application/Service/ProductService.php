<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\ProductResponseDTO;
use App\Domain\Repository\ProductRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;

class ProductService implements ProductServiceInterface
{
    private const CACHE_TTL = 300; // 5 minutes
    private const CACHE_KEY_PREFIX = 'products_';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly DiscountServiceInterface $discountService,
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    /**
     * @return ProductResponseDTO[]
     */
    public function getProductsWithDiscounts(
        ?string $category = null,
        ?int $priceLessThan = null,
        int $limit = 5,
        int $offset = 0
    ): array {
        $cacheKey = $this->generateCacheKey($category, $priceLessThan, $limit, $offset);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $products = $this->productRepository->findWithFilters($category, $priceLessThan, $limit, $offset);
        
        $productDTOs = array_map(
            fn($product) => $this->discountService->applyDiscounts($product),
            $products
        );

        $cacheItem->set($productDTOs);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        $this->cache->save($cacheItem);

        return $productDTOs;
    }

    public function getTotalCount(?string $category = null, ?int $priceLessThan = null): int
    {
        return $this->productRepository->countWithFilters($category, $priceLessThan);
    }

    private function generateCacheKey(?string $category, ?int $priceLessThan, int $limit, int $offset): string
    {
        return self::CACHE_KEY_PREFIX . md5(serialize([
            'category' => $category,
            'priceLessThan' => $priceLessThan,
            'limit' => $limit,
            'offset' => $offset
        ]));
    }
}