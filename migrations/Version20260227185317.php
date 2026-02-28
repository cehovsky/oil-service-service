<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227185317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_sepno_record (id CHAR(36) NOT NULL, status VARCHAR(255) NOT NULL, official_sepno_id VARCHAR(64) DEFAULT NULL, request_xml LONGTEXT DEFAULT NULL, response_xml LONGTEXT DEFAULT NULL, estimated_waste_kg DOUBLE PRECISION DEFAULT NULL, actual_waste_kg DOUBLE PRECISION DEFAULT NULL, source VARCHAR(32) NOT NULL, last_error LONGTEXT DEFAULT NULL, submitted_at DATETIME DEFAULT NULL, closed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, route_id CHAR(36) NOT NULL, response_file_id CHAR(36) DEFAULT NULL, created_by_user_id CHAR(36) DEFAULT NULL, INDEX IDX_1DAB26E738713F12 (response_file_id), INDEX IDX_1DAB26E77D182D95 (created_by_user_id), INDEX idx_sepno_route (route_id), INDEX idx_sepno_status (status), INDEX idx_sepno_created (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_sepno_record ADD CONSTRAINT FK_1DAB26E734ECB4E6 FOREIGN KEY (route_id) REFERENCES oil_service_route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_sepno_record ADD CONSTRAINT FK_1DAB26E738713F12 FOREIGN KEY (response_file_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_sepno_record ADD CONSTRAINT FK_1DAB26E77D182D95 FOREIGN KEY (created_by_user_id) REFERENCES auth_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_route ADD current_sepno_record_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_route ADD CONSTRAINT FK_4A240E4FD420957F FOREIGN KEY (current_sepno_record_id) REFERENCES oil_service_sepno_record (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4A240E4FD420957F ON oil_service_route (current_sepno_record_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_sepno_record DROP FOREIGN KEY FK_1DAB26E734ECB4E6');
        $this->addSql('ALTER TABLE oil_service_sepno_record DROP FOREIGN KEY FK_1DAB26E738713F12');
        $this->addSql('ALTER TABLE oil_service_sepno_record DROP FOREIGN KEY FK_1DAB26E77D182D95');
        $this->addSql('DROP TABLE oil_service_sepno_record');
        $this->addSql('ALTER TABLE oil_service_route DROP FOREIGN KEY FK_4A240E4FD420957F');
        $this->addSql('DROP INDEX IDX_4A240E4FD420957F ON oil_service_route');
        $this->addSql('ALTER TABLE oil_service_route DROP current_sepno_record_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
