<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251231120001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add oil_service_car, oil_service_term, oil_service_route entities and term relation to forms.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE oil_service_car (id CHAR(36) NOT NULL, label VARCHAR(255) NOT NULL, ident VARCHAR(10) NOT NULL, license_plate VARCHAR(20) NOT NULL, status VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_229D7F594E951C2 (ident), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_term (id CHAR(36) NOT NULL, date DATE NOT NULL, time_slot VARCHAR(32) NOT NULL, is_active TINYINT NOT NULL, max_count INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_route (id CHAR(36) NOT NULL, car_id CHAR(36) DEFAULT NULL, is_active TINYINT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_AE6D8A43C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_route_term (route_id CHAR(36) NOT NULL, term_id CHAR(36) NOT NULL, INDEX IDX_EAFB2EFD59027487 (route_id), INDEX IDX_EAFB2EFD1AD5CDBF (term_id), PRIMARY KEY(route_id, term_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_form ADD term_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_form ADD CONSTRAINT FK_7A84FE4AF8BF24A7 FOREIGN KEY (term_id) REFERENCES oil_service_term (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7A84FE4AF8BF24A7 ON oil_service_form (term_id)');
        $this->addSql('ALTER TABLE oil_service_route ADD CONSTRAINT FK_AE6D8A43C3C6F69F FOREIGN KEY (car_id) REFERENCES oil_service_car (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_route_term ADD CONSTRAINT FK_EAFB2EFD59027487 FOREIGN KEY (route_id) REFERENCES oil_service_route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_route_term ADD CONSTRAINT FK_EAFB2EFD1AD5CDBF FOREIGN KEY (term_id) REFERENCES oil_service_term (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oil_service_form DROP FOREIGN KEY FK_7A84FE4AF8BF24A7');
        $this->addSql('ALTER TABLE oil_service_route DROP FOREIGN KEY FK_AE6D8A43C3C6F69F');
        $this->addSql('ALTER TABLE oil_service_route_term DROP FOREIGN KEY FK_EAFB2EFD59027487');
        $this->addSql('ALTER TABLE oil_service_route_term DROP FOREIGN KEY FK_EAFB2EFD1AD5CDBF');
        $this->addSql('DROP TABLE oil_service_route_term');
        $this->addSql('DROP TABLE oil_service_route');
        $this->addSql('DROP TABLE oil_service_term');
        $this->addSql('DROP TABLE oil_service_car');
        $this->addSql('DROP INDEX IDX_7A84FE4AF8BF24A7 ON oil_service_form');
        $this->addSql('ALTER TABLE oil_service_form DROP term_id');
    }
}
