<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260120120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_inventory_item CHANGE price price NUMERIC(16, 2) DEFAULT NULL, CHANGE vat vat INT DEFAULT NULL, CHANGE price_vat price_vat NUMERIC(16, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE oil_service_inventory_item SET price = '0.00' WHERE price IS NULL");
        $this->addSql('UPDATE oil_service_inventory_item SET vat = 21 WHERE vat IS NULL');
        $this->addSql("UPDATE oil_service_inventory_item SET price_vat = '0.00' WHERE price_vat IS NULL");
        $this->addSql('ALTER TABLE oil_service_inventory_item CHANGE price price NUMERIC(16, 2) NOT NULL, CHANGE vat vat INT NOT NULL DEFAULT 21, CHANGE price_vat price_vat NUMERIC(16, 2) NOT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
