<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\ProductResponseDTO;

interface ProductServiceInterface
{
    /**
     * @return ProductResponseDTO[]
     */
    public function getProductsWithDiscounts(
        ?string $category = null,
        ?int $priceLessThan = null,
        int $limit = 5
    ): array;

    public function getTotalCount(?string $category = null, ?int $priceLessThan = null): int;
}