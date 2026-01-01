<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260101203905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_car RENAME INDEX uniq_229d7f594e951c2 TO UNIQ_7A2772E44E78B2');
        $this->addSql('ALTER TABLE oil_service_form DROP FOREIGN KEY `FK_7A84FE4AF8BF24A7`');
        $this->addSql('DROP INDEX IDX_7A84FE4AF8BF24A7 ON oil_service_form');
        $this->addSql('ALTER TABLE oil_service_form CHANGE status status VARCHAR(255) NOT NULL, CHANGE realization_time_slot realization_time_slot VARCHAR(255) NOT NULL, CHANGE term_id route_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_form ADD CONSTRAINT FK_9080E834ECB4E6 FOREIGN KEY (route_id) REFERENCES oil_service_route (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9080E834ECB4E6 ON oil_service_form (route_id)');
        $this->addSql('ALTER TABLE oil_service_form RENAME INDEX uniq_9080e8ident TO UNIQ_9080E844E78B2');
        $this->addSql('ALTER TABLE oil_service_route RENAME INDEX idx_ae6d8a43c3c6f69f TO IDX_4A240E4FC3C6F69F');
        $this->addSql('ALTER TABLE oil_service_route_term RENAME INDEX idx_eafb2efd59027487 TO IDX_F784668E34ECB4E6');
        $this->addSql('ALTER TABLE oil_service_route_term RENAME INDEX idx_eafb2efd1ad5cdbf TO IDX_F784668EE2C35FC');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_car RENAME INDEX uniq_7a2772e44e78b2 TO UNIQ_229D7F594E951C2');
        $this->addSql('ALTER TABLE oil_service_form DROP FOREIGN KEY FK_9080E834ECB4E6');
        $this->addSql('DROP INDEX IDX_9080E834ECB4E6 ON oil_service_form');
        $this->addSql('ALTER TABLE oil_service_form CHANGE status status VARCHAR(32) NOT NULL, CHANGE realization_time_slot realization_time_slot VARCHAR(16) NOT NULL, CHANGE route_id term_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_form ADD CONSTRAINT `FK_7A84FE4AF8BF24A7` FOREIGN KEY (term_id) REFERENCES oil_service_term (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7A84FE4AF8BF24A7 ON oil_service_form (term_id)');
        $this->addSql('ALTER TABLE oil_service_form RENAME INDEX uniq_9080e844e78b2 TO UNIQ_9080E8IDENT');
        $this->addSql('ALTER TABLE oil_service_route RENAME INDEX idx_4a240e4fc3c6f69f TO IDX_AE6D8A43C3C6F69F');
        $this->addSql('ALTER TABLE oil_service_route_term RENAME INDEX idx_f784668ee2c35fc TO IDX_EAFB2EFD1AD5CDBF');
        $this->addSql('ALTER TABLE oil_service_route_term RENAME INDEX idx_f784668e34ecb4e6 TO IDX_EAFB2EFD59027487');
    }
}
