<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-products',
    description: 'Seed the database with sample products'
)]
class SeedProductsCommand extends Command
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
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding Products');

        // Clear existing products
        $this->entityManager->createQuery('DELETE FROM App\Domain\Entity\Product')->execute();

        $io->text('Cleared existing products...');

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

        $io->success(sprintf('Successfully seeded %d products!', count(self::SAMPLE_PRODUCTS)));

        return Command::SUCCESS;
    }
}