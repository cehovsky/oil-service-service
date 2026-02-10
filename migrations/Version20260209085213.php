<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209085213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car_database_engine (id CHAR(36) NOT NULL, manufacturer VARCHAR(120) NOT NULL, model VARCHAR(255) NOT NULL, generation VARCHAR(64) DEFAULT NULL, engine_code VARCHAR(64) DEFAULT NULL, engine_family VARCHAR(64) DEFAULT NULL, displacement_cc INT DEFAULT NULL, power_kw INT DEFAULT NULL, fuel VARCHAR(32) DEFAULT NULL, emission_standard VARCHAR(32) DEFAULT NULL, production_from_year INT DEFAULT NULL, production_to_year INT DEFAULT NULL, oil_capacity_l NUMERIC(6, 2) DEFAULT NULL, oil_capacity_note VARCHAR(255) DEFAULT NULL, oil_viscosity VARCHAR(32) DEFAULT NULL, oil_specification VARCHAR(128) DEFAULT NULL, oil_interval_km INT DEFAULT NULL, oil_interval_months INT DEFAULT NULL, oil_drain_plug_torque_nm INT DEFAULT NULL, oil_filter_torque_nm INT DEFAULT NULL, spark_plug_torque_nm INT DEFAULT NULL, source VARCHAR(255) DEFAULT NULL, confidence SMALLINT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_engine_manufacturer (manufacturer), INDEX idx_engine_model (model), INDEX idx_engine_code (engine_code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE car_database_engine_filter (id CHAR(36) NOT NULL, is_primary TINYINT NOT NULL, source VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, engine_id CHAR(36) NOT NULL, filter_id CHAR(36) NOT NULL, INDEX idx_engine_filter_engine (engine_id), INDEX idx_engine_filter_filter (filter_id), UNIQUE INDEX car_database_engine_filter_unique (engine_id, filter_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE car_database_filter (id CHAR(36) NOT NULL, filter_type VARCHAR(32) NOT NULL, manufacturer VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, oem_code VARCHAR(255) DEFAULT NULL, thread VARCHAR(64) DEFAULT NULL, height_mm INT DEFAULT NULL, diameter_mm INT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_filter_type (filter_type), INDEX idx_filter_manufacturer (manufacturer), INDEX idx_filter_code (code), INDEX idx_filter_oem_code (oem_code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE car_database_engine_filter ADD CONSTRAINT FK_1A5B04E9E78C9C0A FOREIGN KEY (engine_id) REFERENCES car_database_engine (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE car_database_engine_filter ADD CONSTRAINT FK_1A5B04E9D395B25E FOREIGN KEY (filter_id) REFERENCES car_database_filter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_customer_car ADD engine_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_customer_car ADD CONSTRAINT FK_B8AFFB48E78C9C0A FOREIGN KEY (engine_id) REFERENCES car_database_engine (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_engine_id ON oil_service_customer_car (engine_id)');
        $this->addSql('ALTER TABLE oil_service_inventory_item CHANGE external_code oem_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_oem_code ON oil_service_inventory_item (oem_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car_database_engine_filter DROP FOREIGN KEY FK_1A5B04E9E78C9C0A');
        $this->addSql('ALTER TABLE car_database_engine_filter DROP FOREIGN KEY FK_1A5B04E9D395B25E');
        $this->addSql('DROP TABLE car_database_engine');
        $this->addSql('DROP TABLE car_database_engine_filter');
        $this->addSql('DROP TABLE car_database_filter');
        $this->addSql('ALTER TABLE oil_service_customer_car DROP FOREIGN KEY FK_B8AFFB48E78C9C0A');
        $this->addSql('DROP INDEX idx_engine_id ON oil_service_customer_car');
        $this->addSql('ALTER TABLE oil_service_customer_car DROP engine_id');
        $this->addSql('DROP INDEX idx_oem_code ON oil_service_inventory_item');
        $this->addSql('ALTER TABLE oil_service_inventory_item CHANGE oem_code external_code VARCHAR(255) DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
