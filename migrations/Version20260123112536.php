<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123112536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ident columns to chat session/user request, link sessions to orders, and reset chat data.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM oil_service_chat_message');
        $this->addSql('DELETE FROM oil_service_chat_user_request');
        $this->addSql('DELETE FROM oil_service_chat_session');
        $this->addSql('ALTER TABLE oil_service_chat_session ADD ident INT NOT NULL, ADD order_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_chat_session ADD CONSTRAINT FK_1C2B5A9E8D9F6D38 FOREIGN KEY (order_id) REFERENCES oil_service_order (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C2B5A9E44E78B2 ON oil_service_chat_session (ident)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C2B5A9E8D9F6D38 ON oil_service_chat_session (order_id)');
        $this->addSql('ALTER TABLE oil_service_chat_user_request ADD ident INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E027634F44E78B2 ON oil_service_chat_user_request (ident)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_chat_session DROP FOREIGN KEY FK_1C2B5A9E8D9F6D38');
        $this->addSql('DROP INDEX UNIQ_1C2B5A9E44E78B2 ON oil_service_chat_session');
        $this->addSql('DROP INDEX UNIQ_1C2B5A9E8D9F6D38 ON oil_service_chat_session');
        $this->addSql('ALTER TABLE oil_service_chat_session DROP ident, DROP order_id');
        $this->addSql('DROP INDEX UNIQ_E027634F44E78B2 ON oil_service_chat_user_request');
        $this->addSql('ALTER TABLE oil_service_chat_user_request DROP ident');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
