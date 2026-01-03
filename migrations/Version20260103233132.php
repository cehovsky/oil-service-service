<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103233132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE warehouse_storage_container_preferred_waste_materials (storage_container_id CHAR(36) NOT NULL, waste_material_id CHAR(36) NOT NULL, INDEX IDX_251BF2C5F04277E5 (storage_container_id), INDEX IDX_251BF2C52BBE62AB (waste_material_id), PRIMARY KEY (storage_container_id, waste_material_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE warehouse_storage_container_preferred_waste_materials ADD CONSTRAINT FK_251BF2C5F04277E5 FOREIGN KEY (storage_container_id) REFERENCES warehouse_storage_container (id)');
        $this->addSql('ALTER TABLE warehouse_storage_container_preferred_waste_materials ADD CONSTRAINT FK_251BF2C52BBE62AB FOREIGN KEY (waste_material_id) REFERENCES warehouse_waste_material (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE warehouse_storage_container_preferred_waste_materials DROP FOREIGN KEY FK_251BF2C5F04277E5');
        $this->addSql('ALTER TABLE warehouse_storage_container_preferred_waste_materials DROP FOREIGN KEY FK_251BF2C52BBE62AB');
        $this->addSql('DROP TABLE warehouse_storage_container_preferred_waste_materials');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
