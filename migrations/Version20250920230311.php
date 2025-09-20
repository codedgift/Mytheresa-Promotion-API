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
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            UNIQUE INDEX UNIQ_B3BA5A5AF9038C4 (sku), 
            INDEX idx_product_category (category), 
            INDEX idx_product_price (price), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products');
    }
}
