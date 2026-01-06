<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260106180251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE warehouse_recycling (id CHAR(36) NOT NULL, recycled_at DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, recycled_by_id CHAR(36) NOT NULL, INDEX IDX_C97D12B7E5ADD33D (recycled_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE warehouse_storage_container_material (id CHAR(36) NOT NULL, volume DOUBLE PRECISION NOT NULL, is_recycled TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, storage_container_id CHAR(36) NOT NULL, waste_material_id CHAR(36) NOT NULL, warehouse_id CHAR(36) DEFAULT NULL, route_id CHAR(36) DEFAULT NULL, recycling_id CHAR(36) DEFAULT NULL, created_by_id CHAR(36) NOT NULL, updated_by_id CHAR(36) NOT NULL, INDEX IDX_1E81FF67F04277E5 (storage_container_id), INDEX IDX_1E81FF672BBE62AB (waste_material_id), INDEX IDX_1E81FF675080ECDE (warehouse_id), INDEX IDX_1E81FF6734ECB4E6 (route_id), INDEX IDX_1E81FF67D08DEC6C (recycling_id), INDEX IDX_1E81FF67B03A8386 (created_by_id), INDEX IDX_1E81FF67896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE warehouse_storage_container_material_history (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, storage_container_material_id CHAR(36) NOT NULL, storage_container_id CHAR(36) NOT NULL, created_by_id CHAR(36) NOT NULL, INDEX IDX_11D69C3A8D2124FE (storage_container_material_id), INDEX IDX_11D69C3AF04277E5 (storage_container_id), INDEX IDX_11D69C3AB03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE warehouse_recycling ADD CONSTRAINT FK_C97D12B7E5ADD33D FOREIGN KEY (recycled_by_id) REFERENCES auth_user (id)');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF67F04277E5 FOREIGN KEY (storage_container_id) REFERENCES warehouse_storage_container (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF672BBE62AB FOREIGN KEY (waste_material_id) REFERENCES warehouse_waste_material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF675080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse_warehouse (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF6734ECB4E6 FOREIGN KEY (route_id) REFERENCES oil_service_route (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF67D08DEC6C FOREIGN KEY (recycling_id) REFERENCES warehouse_recycling (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF67B03A8386 FOREIGN KEY (created_by_id) REFERENCES auth_user (id)');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF67896DBBDE FOREIGN KEY (updated_by_id) REFERENCES auth_user (id)');
        $this->addSql('ALTER TABLE warehouse_storage_container_material_history ADD CONSTRAINT FK_11D69C3A8D2124FE FOREIGN KEY (storage_container_material_id) REFERENCES warehouse_storage_container_material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material_history ADD CONSTRAINT FK_11D69C3AF04277E5 FOREIGN KEY (storage_container_id) REFERENCES warehouse_storage_container (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material_history ADD CONSTRAINT FK_11D69C3AB03A8386 FOREIGN KEY (created_by_id) REFERENCES auth_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE warehouse_recycling DROP FOREIGN KEY FK_C97D12B7E5ADD33D');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF67F04277E5');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF672BBE62AB');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF675080ECDE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF6734ECB4E6');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF67D08DEC6C');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF67B03A8386');
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF67896DBBDE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material_history DROP FOREIGN KEY FK_11D69C3A8D2124FE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material_history DROP FOREIGN KEY FK_11D69C3AF04277E5');
        $this->addSql('ALTER TABLE warehouse_storage_container_material_history DROP FOREIGN KEY FK_11D69C3AB03A8386');
        $this->addSql('DROP TABLE warehouse_recycling');
        $this->addSql('DROP TABLE warehouse_storage_container_material');
        $this->addSql('DROP TABLE warehouse_storage_container_material_history');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
