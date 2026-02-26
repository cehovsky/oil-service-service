<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225183231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_chat_session CHANGE validated_service_address_at validated_service_address_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_order CHANGE secret_key secret_key VARCHAR(36) NOT NULL');
        $this->addSql('CREATE INDEX idx_secret_key ON oil_service_order (secret_key)');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX uniq_17b4e94793d9e89 TO UNIQ_BDC9BDAE7F4741F5');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_chat_session CHANGE validated_service_address_at validated_service_address_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX idx_secret_key ON oil_service_order');
        $this->addSql('ALTER TABLE oil_service_order CHANGE secret_key secret_key CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE oil_service_order RENAME INDEX uniq_bdc9bdae7f4741f5 TO UNIQ_17B4E94793D9E89');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
