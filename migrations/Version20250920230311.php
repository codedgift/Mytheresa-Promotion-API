<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920230311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products table with indexes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE products (
            id INT AUTO_INCREMENT NOT NULL,
            sku VARCHAR(20) NOT NULL,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            price INT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_PRODUCTS_SKU ON products (sku)');
        $this->addSql('CREATE INDEX IDX_PRODUCTS_CATEGORY ON products (category)');
        $this->addSql('CREATE INDEX IDX_PRODUCTS_PRICE ON products (price)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products');
    }
}
