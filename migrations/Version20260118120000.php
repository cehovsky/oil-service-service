<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260118120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_inventory_item (id CHAR(36) NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(16, 2) NOT NULL, vat INT NOT NULL DEFAULT 21, price_vat NUMERIC(16, 2) NOT NULL, stock_count INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by_id CHAR(36) NOT NULL, updated_by_id CHAR(36) NOT NULL, INDEX IDX_76F62D329DEB7F34 (created_by_id), INDEX IDX_76F62D32AFA11A0E (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_inventory_item_history (id CHAR(36) NOT NULL, inventory_item_id CHAR(36) NOT NULL, order_id CHAR(36) DEFAULT NULL, movement_type VARCHAR(255) NOT NULL, quantity INT NOT NULL, is_increment TINYINT(1) NOT NULL, price NUMERIC(16, 2) DEFAULT NULL, vat INT DEFAULT NULL, price_vat NUMERIC(16, 2) DEFAULT NULL, note LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, created_by_id CHAR(36) NOT NULL, INDEX IDX_7E0E6D9CE2198F31 (inventory_item_id), INDEX IDX_7E0E6D9C8D9F6D38 (order_id), INDEX IDX_7E0E6D9C9DEB7F34 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_order_inventory_item (id CHAR(36) NOT NULL, order_id CHAR(36) NOT NULL, inventory_item_id CHAR(36) NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1651DDAA8D9F6D38 (order_id), INDEX IDX_1651DDAAE2198F31 (inventory_item_id), UNIQUE INDEX oil_service_order_inventory_item_unique (order_id, inventory_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_inventory_item ADD CONSTRAINT FK_76F62D329DEB7F34 FOREIGN KEY (created_by_id) REFERENCES auth_user (id)');
        $this->addSql('ALTER TABLE oil_service_inventory_item ADD CONSTRAINT FK_76F62D32AFA11A0E FOREIGN KEY (updated_by_id) REFERENCES auth_user (id)');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history ADD CONSTRAINT FK_7E0E6D9CE2198F31 FOREIGN KEY (inventory_item_id) REFERENCES oil_service_inventory_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history ADD CONSTRAINT FK_7E0E6D9C8D9F6D38 FOREIGN KEY (order_id) REFERENCES oil_service_order (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history ADD CONSTRAINT FK_7E0E6D9C9DEB7F34 FOREIGN KEY (created_by_id) REFERENCES auth_user (id)');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item ADD CONSTRAINT FK_1651DDAA8D9F6D38 FOREIGN KEY (order_id) REFERENCES oil_service_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item ADD CONSTRAINT FK_1651DDAAE2198F31 FOREIGN KEY (inventory_item_id) REFERENCES oil_service_inventory_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_inventory_item_history DROP FOREIGN KEY FK_7E0E6D9CE2198F31');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history DROP FOREIGN KEY FK_7E0E6D9C8D9F6D38');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history DROP FOREIGN KEY FK_7E0E6D9C9DEB7F34');
        $this->addSql('ALTER TABLE oil_service_inventory_item DROP FOREIGN KEY FK_76F62D329DEB7F34');
        $this->addSql('ALTER TABLE oil_service_inventory_item DROP FOREIGN KEY FK_76F62D32AFA11A0E');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item DROP FOREIGN KEY FK_1651DDAA8D9F6D38');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item DROP FOREIGN KEY FK_1651DDAAE2198F31');
        $this->addSql('DROP TABLE oil_service_inventory_item_history');
        $this->addSql('DROP TABLE oil_service_order_inventory_item');
        $this->addSql('DROP TABLE oil_service_inventory_item');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
