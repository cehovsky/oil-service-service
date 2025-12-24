<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251225204026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_token_access (id CHAR(36) NOT NULL, token CHAR(36) NOT NULL, expires_at DATETIME NOT NULL, refresh_token_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_F9098D525F37A13B (token), INDEX IDX_F9098D52F765F60E (refresh_token_id), INDEX expires_at_index (expires_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE auth_token_refresh (id CHAR(36) NOT NULL, token CHAR(36) NOT NULL, expires_at DATETIME NOT NULL, is_rejected TINYINT NOT NULL, user_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_891D65685F37A13B (token), INDEX IDX_891D6568A76ED395 (user_id), INDEX expires_at_index (expires_at), INDEX is_rejected_index (is_rejected), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE auth_user (id CHAR(36) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, is_active TINYINT NOT NULL, is_admin TINYINT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_A3B536FDE7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE coogle_translate_cache_item (id CHAR(36) NOT NULL, source_language_code VARCHAR(255) NOT NULL, target_language_code VARCHAR(255) NOT NULL, source_text VARCHAR(255) NOT NULL, target_text VARCHAR(255) NOT NULL, INDEX search_index (source_language_code, target_language_code, source_text), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE file (created_at DATETIME NOT NULL, id CHAR(36) NOT NULL, folder VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, size INT NOT NULL, created_user_id CHAR(36) DEFAULT NULL, INDEX IDX_8C9F3610E104C1D3 (created_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lock_keys (key_id VARCHAR(64) NOT NULL, key_token VARCHAR(44) NOT NULL, key_expiration INT UNSIGNED NOT NULL, PRIMARY KEY (key_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE auth_token_access ADD CONSTRAINT FK_F9098D52F765F60E FOREIGN KEY (refresh_token_id) REFERENCES auth_token_refresh (id)');
        $this->addSql('ALTER TABLE auth_token_refresh ADD CONSTRAINT FK_891D6568A76ED395 FOREIGN KEY (user_id) REFERENCES auth_user (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610E104C1D3 FOREIGN KEY (created_user_id) REFERENCES auth_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_token_access DROP FOREIGN KEY FK_F9098D52F765F60E');
        $this->addSql('ALTER TABLE auth_token_refresh DROP FOREIGN KEY FK_891D6568A76ED395');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610E104C1D3');
        $this->addSql('DROP TABLE auth_token_access');
        $this->addSql('DROP TABLE auth_token_refresh');
        $this->addSql('DROP TABLE auth_user');
        $this->addSql('DROP TABLE coogle_translate_cache_item');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE lock_keys');
    }
}
