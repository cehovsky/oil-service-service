<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119233645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_inventory_item ADD code VARCHAR(20) NOT NULL, ADD external_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C10D1A77153098 ON oil_service_inventory_item (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_4C10D1A77153098 ON oil_service_inventory_item');
        $this->addSql('ALTER TABLE oil_service_inventory_item DROP code, DROP external_code');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
