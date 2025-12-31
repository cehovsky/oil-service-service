<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251231110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add status, realization time slot, and realization date columns to oil_service_form.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oil_service_form ADD status VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_form ADD realization_time_slot VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_form ADD realization_date DATE DEFAULT NULL');

        $this->addSql("UPDATE oil_service_form SET status = 'new' WHERE status IS NULL");
        $this->addSql("UPDATE oil_service_form SET realization_time_slot = 'morning' WHERE realization_time_slot IS NULL");
        $this->addSql('UPDATE oil_service_form SET realization_date = DATE(created_at) WHERE realization_date IS NULL');

        $this->addSql('ALTER TABLE oil_service_form MODIFY status VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE oil_service_form MODIFY realization_time_slot VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE oil_service_form MODIFY realization_date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oil_service_form DROP status');
        $this->addSql('ALTER TABLE oil_service_form DROP realization_time_slot');
        $this->addSql('ALTER TABLE oil_service_form DROP realization_date');
    }
}
