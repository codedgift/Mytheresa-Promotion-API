<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class SeedProductsUseCase
{
    private const SAMPLE_PRODUCTS = [
        [
            'sku' => '000001',
            'name' => 'BV Lean leather ankle boots',
            'category' => 'boots',
            'price' => 89000
        ],
        [
            'sku' => '000002',
            'name' => 'BV Lean leather ankle boots',
            'category' => 'boots',
            'price' => 99000
        ],
        [
            'sku' => '000003',
            'name' => 'Ashlington leather ankle boots',
            'category' => 'boots',
            'price' => 71000
        ],
        [
            'sku' => '000004',
            'name' => 'Naima embellished suede sandals',
            'category' => 'sandals',
            'price' => 79500
        ],
        [
            'sku' => '000005',
            'name' => 'Nathane leather sneakers',
            'category' => 'sneakers',
            'price' => 59000
        ]
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function execute(): int
    {
        // Clear existing products
        $this->entityManager->createQuery('DELETE FROM App\Domain\Entity\Product')->execute();

        // Add sample products
        foreach (self::SAMPLE_PRODUCTS as $productData) {
            $product = new Product();
            $product->setSku($productData['sku'])
                   ->setName($productData['name'])
                   ->setCategory($productData['category'])
                   ->setPrice($productData['price']);

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        return count(self::SAMPLE_PRODUCTS);
    }
}