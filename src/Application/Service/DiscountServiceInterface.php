<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Product;
use App\Application\DTO\ProductResponseDTO;

interface DiscountServiceInterface
{
    public function applyDiscounts(Product $product): ProductResponseDTO;
}