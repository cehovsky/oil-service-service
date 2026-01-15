<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114200640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_order_price_list_item (order_id CHAR(36) NOT NULL, price_list_item_id CHAR(36) NOT NULL, INDEX IDX_3D7222B68D9F6D38 (order_id), INDEX IDX_3D7222B63AF3E34E (price_list_item_id), PRIMARY KEY (order_id, price_list_item_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_price_list_item (id CHAR(36) NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, invoice_label VARCHAR(255) DEFAULT NULL, price NUMERIC(16, 2) NOT NULL, vat INT DEFAULT 21 NOT NULL, price_vat NUMERIC(16, 2) NOT NULL, is_active TINYINT NOT NULL, is_public TINYINT NOT NULL, is_default TINYINT NOT NULL, is_hidden_on_invoice TINYINT NOT NULL, code VARCHAR(20) NOT NULL, brand VARCHAR(255) DEFAULT NULL, external_code VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_28E7C3A77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE warehouse_recycling_storage_containers (recycling_id CHAR(36) NOT NULL, storage_container_id CHAR(36) NOT NULL, INDEX IDX_B651B464D08DEC6C (recycling_id), INDEX IDX_B651B464F04277E5 (storage_container_id), PRIMARY KEY (recycling_id, storage_container_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_order_price_list_item ADD CONSTRAINT FK_3D7222B68D9F6D38 FOREIGN KEY (order_id) REFERENCES oil_service_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_order_price_list_item ADD CONSTRAINT FK_3D7222B63AF3E34E FOREIGN KEY (price_list_item_id) REFERENCES oil_service_price_list_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_recycling_storage_containers ADD CONSTRAINT FK_B651B464D08DEC6C FOREIGN KEY (recycling_id) REFERENCES warehouse_recycling (id)');
        $this->addSql('ALTER TABLE warehouse_recycling_storage_containers ADD CONSTRAINT FK_B651B464F04277E5 FOREIGN KEY (storage_container_id) REFERENCES warehouse_storage_container (id)');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX uniq_9080e844e78b2 TO UNIQ_BDC9BDAE44E78B2');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX idx_9080e834ecb4e6 TO IDX_BDC9BDAE34ECB4E6');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX idx_9080e8a76ed395 TO IDX_BDC9BDAEA76ED395');
        $this->addSql('ALTER TABLE warehouse_recycling CHANGE recycled_at recycled_at DATE DEFAULT NULL, CHANGE recycled_by_id recycled_by_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_wscm_order_id TO IDX_1E81FF678D9F6D38');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_order_price_list_item DROP FOREIGN KEY FK_3D7222B68D9F6D38');
        $this->addSql('ALTER TABLE oil_service_order_price_list_item DROP FOREIGN KEY FK_3D7222B63AF3E34E');
        $this->addSql('ALTER TABLE warehouse_recycling_storage_containers DROP FOREIGN KEY FK_B651B464D08DEC6C');
        $this->addSql('ALTER TABLE warehouse_recycling_storage_containers DROP FOREIGN KEY FK_B651B464F04277E5');
        $this->addSql('DROP TABLE oil_service_order_price_list_item');
        $this->addSql('DROP TABLE oil_service_price_list_item');
        $this->addSql('DROP TABLE warehouse_recycling_storage_containers');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX idx_bdc9bdae34ecb4e6 TO IDX_9080E834ECB4E6');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX idx_bdc9bdaea76ed395 TO IDX_9080E8A76ED395');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX uniq_bdc9bdae44e78b2 TO UNIQ_9080E844E78B2');
        $this->addSql('ALTER TABLE warehouse_recycling CHANGE recycled_at recycled_at DATE NOT NULL, CHANGE recycled_by_id recycled_by_id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_1e81ff678d9f6d38 TO IDX_WSCM_ORDER_ID');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
