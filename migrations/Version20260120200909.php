<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260120200909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_chat_knowledge_item (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, language VARCHAR(10) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_chat_message (id CHAR(36) NOT NULL, role VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, session_id CHAR(36) NOT NULL, INDEX IDX_7AD2BF35613FECDF (session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_chat_session (id CHAR(36) NOT NULL, language VARCHAR(10) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, closed_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_chat_user_request (id CHAR(36) NOT NULL, content LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, resolved_at DATETIME DEFAULT NULL, session_id CHAR(36) DEFAULT NULL, INDEX IDX_E027634F613FECDF (session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_chat_message ADD CONSTRAINT FK_7AD2BF35613FECDF FOREIGN KEY (session_id) REFERENCES oil_service_chat_session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_chat_user_request ADD CONSTRAINT FK_E027634F613FECDF FOREIGN KEY (session_id) REFERENCES oil_service_chat_session (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_chat_message DROP FOREIGN KEY FK_7AD2BF35613FECDF');
        $this->addSql('ALTER TABLE oil_service_chat_user_request DROP FOREIGN KEY FK_E027634F613FECDF');
        $this->addSql('DROP TABLE oil_service_chat_knowledge_item');
        $this->addSql('DROP TABLE oil_service_chat_message');
        $this->addSql('DROP TABLE oil_service_chat_session');
        $this->addSql('DROP TABLE oil_service_chat_user_request');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
