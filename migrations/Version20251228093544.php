<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251228093544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_form (id CHAR(36) NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, car_model VARCHAR(255) NOT NULL, license_plate VARCHAR(20) NOT NULL, address VARCHAR(500) NOT NULL, note VARCHAR(1000) DEFAULT NULL, is_company TINYINT NOT NULL, company_name VARCHAR(255) DEFAULT NULL, company_identification_number VARCHAR(20) DEFAULT NULL, company_tax_id VARCHAR(20) DEFAULT NULL, company_address VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, user_id CHAR(36) NOT NULL, INDEX IDX_9080E8A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_user (id CHAR(36) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(50) NOT NULL, full_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_DF8BABEEE7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_form ADD CONSTRAINT FK_9080E8A76ED395 FOREIGN KEY (user_id) REFERENCES oil_service_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_form DROP FOREIGN KEY FK_9080E8A76ED395');
        $this->addSql('DROP TABLE oil_service_form');
        $this->addSql('DROP TABLE oil_service_user');
    }
}
