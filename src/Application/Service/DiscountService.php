<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Product;
use App\Domain\ValueObject\Price;
use App\Application\DTO\ProductResponseDTO;

class DiscountService implements DiscountServiceInterface
{
    private const BOOTS_DISCOUNT = 30;
    private const SKU_000003_DISCOUNT = 15;
    private const SPECIAL_SKU = '000003';
    private const BOOTS_CATEGORY = 'boots';
    private const DEFAULT_CURRENCY = 'EUR';
    private const DEFAULT_PERCENTAGE_VALUE = 100;

    public function applyDiscounts(Product $product): ProductResponseDTO
    {
        $originalPrice = $product->getPrice();
        $discountPercentage = $this->calculateDiscountPercentage($product);
        
        $finalPrice = $discountPercentage > 0 
            ? $this->calculateDiscountedPrice($originalPrice, $discountPercentage)
            : $originalPrice;

        $discountPercentageString = $discountPercentage > 0 
            ? $discountPercentage . '%' 
            : null;

        $price = new Price(
            $originalPrice,
            $finalPrice,
            $discountPercentageString,
            self::DEFAULT_CURRENCY
        );

        return new ProductResponseDTO(
            $product->getSku(),
            $product->getName(),
            $product->getCategory(),
            $price
        );
    }

    private function calculateDiscountPercentage(Product $product): int
    {
        $discounts = [];

        // Apply category-based discount
        if ($product->getCategory() === self::BOOTS_CATEGORY) {
            $discounts[] = self::BOOTS_DISCOUNT;
        }

        // Apply SKU-based discount
        if ($product->getSku() === self::SPECIAL_SKU) {
            $discounts[] = self::SKU_000003_DISCOUNT;
        }

        // When multiple discounts collide, the bigger discount must be applied
        return empty($discounts) ? 0 : max($discounts);
    }

    private function calculateDiscountedPrice(int $originalPrice, int $discountPercentage): int
    {
        $discountAmount = ($originalPrice * $discountPercentage) / self::DEFAULT_PERCENTAGE_VALUE;
        return (int) round($originalPrice - $discountAmount);
    }
}