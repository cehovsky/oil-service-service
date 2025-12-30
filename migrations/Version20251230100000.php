<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add ident column to oil_service_form table for auto-incrementing form identifier.
 */
final class Version20251230100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ident column to oil_service_form table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oil_service_form ADD ident INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9080E8IDENT ON oil_service_form (ident)');

        // Set initial ident values for existing records based on creation order
        $this->addSql('SET @counter = 0');
        $this->addSql('UPDATE oil_service_form SET ident = (@counter := @counter + 1) ORDER BY created_at ASC');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_9080E8IDENT ON oil_service_form');
        $this->addSql('ALTER TABLE oil_service_form DROP ident');
    }
}
