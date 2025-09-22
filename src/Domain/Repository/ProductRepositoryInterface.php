<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return Product[]
     */
    public function findWithFilters(?string $category = null, ?int $priceLessThan = null, int $limit = 5, int $offset = 0): array;

    public function countWithFilters(?string $category = null, ?int $priceLessThan = null): int;

    public function save(Product $product, bool $flush = false): void;

    public function remove(Product $product, bool $flush = false): void;
}