<?php

declare(strict_types=1);

namespace App\Application\DTO;

class GetProductsResponse
{
    /**
     * @param ProductResponseDTO[] $products
     */
    public function __construct(
        public readonly array $products,
        public readonly int $totalItems,
        public readonly int $currentPage,
        public readonly int $itemsPerPage,
        public readonly int $totalPages
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn($product) => [
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'price' => [
                    'original' => $product->price->original,
                    'final' => $product->price->final,
                    'discount_percentage' => $product->price->discount_percentage,
                    'currency' => $product->price->currency,
                ]
            ], $this->products),
            'pagination' => [
                'current_page' => $this->currentPage,
                'items_per_page' => $this->itemsPerPage,
                'total_items' => $this->totalItems,
                'total_pages' => $this->totalPages,
            ]
        ];
    }
}