<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260102234614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE warehouse_storage_container (id CHAR(36) NOT NULL, code VARCHAR(20) NOT NULL, description VARCHAR(255) DEFAULT NULL, is_active TINYINT NOT NULL, type VARCHAR(64) NOT NULL, capacity DOUBLE PRECISION NOT NULL, volume_unit VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_5A81919C77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE warehouse_waste_material (id CHAR(36) NOT NULL, code VARCHAR(20) NOT NULL, label VARCHAR(255) NOT NULL, short_label VARCHAR(255) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_59B01A6F77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE warehouse_storage_container');
        $this->addSql('DROP TABLE warehouse_waste_material');
    }
}
