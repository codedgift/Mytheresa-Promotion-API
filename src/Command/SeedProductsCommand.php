<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\UseCase\SeedProductsUseCase;
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
    public function __construct(
        private readonly SeedProductsUseCase $seedProductsUseCase
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Seeding Products');

        $count = $this->seedProductsUseCase->execute();

        $io->success(sprintf('Successfully seeded %d products!', $count));

        return Command::SUCCESS;
    }
}