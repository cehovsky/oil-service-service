<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104004942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_route_user (id CHAR(36) NOT NULL, created_at DATETIME NOT NULL, route_id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL, INDEX IDX_DF18574A34ECB4E6 (route_id), INDEX IDX_DF18574AA76ED395 (user_id), UNIQUE INDEX oil_service_route_user_route_user_unique (route_id, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_route_user ADD CONSTRAINT FK_DF18574A34ECB4E6 FOREIGN KEY (route_id) REFERENCES oil_service_route (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_route_user ADD CONSTRAINT FK_DF18574AA76ED395 FOREIGN KEY (user_id) REFERENCES auth_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_route_user DROP FOREIGN KEY FK_DF18574A34ECB4E6');
        $this->addSql('ALTER TABLE oil_service_route_user DROP FOREIGN KEY FK_DF18574AA76ED395');
        $this->addSql('DROP TABLE oil_service_route_user');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
