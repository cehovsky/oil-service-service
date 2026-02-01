<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260201111445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_status ON oil_service_car (status)');
        $this->addSql('CREATE INDEX idx_session_created ON oil_service_chat_message (session_id, created_at)');
        $this->addSql('CREATE INDEX idx_status ON oil_service_chat_session (status)');
        $this->addSql('CREATE INDEX idx_realization_date_timeslot_status ON oil_service_order (realization_date, realization_time_slot, status)');
        $this->addSql('CREATE INDEX idx_route_position ON oil_service_order (route_id, route_order_position)');
        $this->addSql('CREATE INDEX idx_status ON oil_service_order (status)');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX idx_bdc9bdaea76ed395 TO idx_user');
        $this->addSql('CREATE INDEX idx_active_date ON oil_service_route (is_active, date)');
        $this->addSql('CREATE INDEX idx_date_created ON oil_service_route (date, created_at)');
        $this->addSql('ALTER TABLE oil_service_route RENAME INDEX idx_4a240e4fc3c6f69f TO idx_car');
        $this->addSql('CREATE INDEX idx_user_route ON oil_service_route_user (user_id, route_id)');
        $this->addSql('CREATE INDEX idx_active_date ON oil_service_term (is_active, date)');
        $this->addSql('CREATE UNIQUE INDEX uniq_date_timeslot ON oil_service_term (date, time_slot)');
        $this->addSql('CREATE INDEX idx_recycled_at ON warehouse_recycling (recycled_at)');
        $this->addSql('ALTER TABLE warehouse_recycling RENAME INDEX idx_c97d12b7e5add33d TO idx_recycled_by');
        $this->addSql('CREATE INDEX idx_container_moved ON warehouse_storage_container_location (storage_container_id, moved_at)');
        $this->addSql('ALTER TABLE warehouse_storage_container_location RENAME INDEX idx_3ca103395080ecde TO idx_warehouse');
        $this->addSql('ALTER TABLE warehouse_storage_container_location RENAME INDEX idx_3ca1033934ecb4e6 TO idx_route');
        $this->addSql('CREATE INDEX idx_container_recycled ON warehouse_storage_container_material (storage_container_id, is_recycled)');
        $this->addSql('CREATE INDEX idx_created_at ON warehouse_storage_container_material (created_at)');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_1e81ff675080ecde TO idx_warehouse');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_1e81ff6734ecb4e6 TO idx_route');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_1e81ff678d9f6d38 TO idx_order');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_1e81ff67d08dec6c TO idx_recycling');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_status ON oil_service_car');
        $this->addSql('DROP INDEX idx_session_created ON oil_service_chat_message');
        $this->addSql('DROP INDEX idx_status ON oil_service_chat_session');
        $this->addSql('DROP INDEX idx_realization_date_timeslot_status ON oil_service_order');
        $this->addSql('DROP INDEX idx_route_position ON oil_service_order');
        $this->addSql('DROP INDEX idx_status ON oil_service_order');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX idx_user TO IDX_BDC9BDAEA76ED395');
        $this->addSql('DROP INDEX idx_active_date ON oil_service_route');
        $this->addSql('DROP INDEX idx_date_created ON oil_service_route');
        $this->addSql('ALTER TABLE oil_service_route RENAME INDEX idx_car TO IDX_4A240E4FC3C6F69F');
        $this->addSql('DROP INDEX idx_user_route ON oil_service_route_user');
        $this->addSql('DROP INDEX idx_active_date ON oil_service_term');
        $this->addSql('DROP INDEX uniq_date_timeslot ON oil_service_term');
        $this->addSql('DROP INDEX idx_recycled_at ON warehouse_recycling');
        $this->addSql('ALTER TABLE warehouse_recycling RENAME INDEX idx_recycled_by TO IDX_C97D12B7E5ADD33D');
        $this->addSql('DROP INDEX idx_container_moved ON warehouse_storage_container_location');
        $this->addSql('ALTER TABLE warehouse_storage_container_location RENAME INDEX idx_warehouse TO IDX_3CA103395080ECDE');
        $this->addSql('ALTER TABLE warehouse_storage_container_location RENAME INDEX idx_route TO IDX_3CA1033934ECB4E6');
        $this->addSql('DROP INDEX idx_container_recycled ON warehouse_storage_container_material');
        $this->addSql('DROP INDEX idx_created_at ON warehouse_storage_container_material');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_warehouse TO IDX_1E81FF675080ECDE');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_route TO IDX_1E81FF6734ECB4E6');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_recycling TO IDX_1E81FF67D08DEC6C');
        $this->addSql('ALTER TABLE warehouse_storage_container_material RENAME INDEX idx_order TO IDX_1E81FF678D9F6D38');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
