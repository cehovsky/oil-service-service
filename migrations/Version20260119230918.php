<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119230918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_inventory_item RENAME INDEX idx_76f62d329deb7f34 TO IDX_4C10D1AB03A8386');
        $this->addSql('ALTER TABLE oil_service_inventory_item RENAME INDEX idx_76f62d32afa11a0e TO IDX_4C10D1A896DBBDE');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history RENAME INDEX idx_7e0e6d9ce2198f31 TO IDX_F06AB6C4536BF4A2');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history RENAME INDEX idx_7e0e6d9c8d9f6d38 TO IDX_F06AB6C48D9F6D38');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history RENAME INDEX idx_7e0e6d9c9deb7f34 TO IDX_F06AB6C4B03A8386');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item RENAME INDEX idx_1651ddaa8d9f6d38 TO IDX_4D995CBC8D9F6D38');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item RENAME INDEX idx_1651ddaae2198f31 TO IDX_4D995CBC536BF4A2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_inventory_item RENAME INDEX idx_4c10d1ab03a8386 TO IDX_76F62D329DEB7F34');
        $this->addSql('ALTER TABLE oil_service_inventory_item RENAME INDEX idx_4c10d1a896dbbde TO IDX_76F62D32AFA11A0E');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history RENAME INDEX idx_f06ab6c48d9f6d38 TO IDX_7E0E6D9C8D9F6D38');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history RENAME INDEX idx_f06ab6c4b03a8386 TO IDX_7E0E6D9C9DEB7F34');
        $this->addSql('ALTER TABLE oil_service_inventory_item_history RENAME INDEX idx_f06ab6c4536bf4a2 TO IDX_7E0E6D9CE2198F31');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item RENAME INDEX idx_4d995cbc536bf4a2 TO IDX_1651DDAAE2198F31');
        $this->addSql('ALTER TABLE oil_service_order_inventory_item RENAME INDEX idx_4d995cbc8d9f6d38 TO IDX_1651DDAA8D9F6D38');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
