<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260106194000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename oil_service_form table to oil_service_order and migrate related foreign keys without data loss.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_1E81FF67A67B20B8');
        $this->addSql('DROP INDEX IDX_1E81FF67A67B20B8 ON warehouse_storage_container_material');
        $this->addSql('ALTER TABLE warehouse_storage_container_material CHANGE form_id order_id CHAR(36) DEFAULT NULL');
        $this->addSql('RENAME TABLE oil_service_form TO oil_service_order');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_WSCM_ORDER_ID FOREIGN KEY (order_id) REFERENCES oil_service_order (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_WSCM_ORDER_ID ON warehouse_storage_container_material (order_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE warehouse_storage_container_material DROP FOREIGN KEY FK_WSCM_ORDER_ID');
        $this->addSql('DROP INDEX IDX_WSCM_ORDER_ID ON warehouse_storage_container_material');
        $this->addSql('RENAME TABLE oil_service_order TO oil_service_form');
        $this->addSql('ALTER TABLE warehouse_storage_container_material CHANGE order_id form_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_material ADD CONSTRAINT FK_1E81FF67A67B20B8 FOREIGN KEY (form_id) REFERENCES oil_service_form (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1E81FF67A67B20B8 ON warehouse_storage_container_material (form_id)');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
