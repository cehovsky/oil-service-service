<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103202252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE warehouse_storage_container_location (id CHAR(36) NOT NULL, moved_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, storage_container_id CHAR(36) NOT NULL, warehouse_id CHAR(36) DEFAULT NULL, route_id CHAR(36) DEFAULT NULL, INDEX IDX_3CA10339F04277E5 (storage_container_id), INDEX IDX_3CA103395080ECDE (warehouse_id), INDEX IDX_3CA1033934ECB4E6 (route_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE warehouse_storage_container_location ADD CONSTRAINT FK_3CA10339F04277E5 FOREIGN KEY (storage_container_id) REFERENCES warehouse_storage_container (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_storage_container_location ADD CONSTRAINT FK_3CA103395080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse_warehouse (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_location ADD CONSTRAINT FK_3CA1033934ECB4E6 FOREIGN KEY (route_id) REFERENCES oil_service_route (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE warehouse_storage_container_location DROP FOREIGN KEY FK_3CA10339F04277E5');
        $this->addSql('ALTER TABLE warehouse_storage_container_location DROP FOREIGN KEY FK_3CA103395080ECDE');
        $this->addSql('ALTER TABLE warehouse_storage_container_location DROP FOREIGN KEY FK_3CA1033934ECB4E6');
        $this->addSql('DROP TABLE warehouse_storage_container_location');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
