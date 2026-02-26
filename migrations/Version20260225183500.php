<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225183500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add random secret_key for public order reports.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oil_service_order ADD secret_key CHAR(36) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17B4E94793D9E89 ON oil_service_order (secret_key)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_17B4E94793D9E89 ON oil_service_order');
        $this->addSql('ALTER TABLE oil_service_order DROP secret_key');
    }
}
