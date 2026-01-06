<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260106191500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD form_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF67A67B20B8 FOREIGN KEY (form_id) REFERENCES oil_service_form (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1E81FF67A67B20B8 ON warehouse_storage_container_material (form_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF67A67B20B8');
        $this->addSql('DROP INDEX IDX_1E81FF67A67B20B8 ON warehouse_storage_container_material');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP form_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
