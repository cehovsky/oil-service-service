<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223213000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Persist last validated chat service address state on chat sessions.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE oil_service_chat_session ADD validated_service_address VARCHAR(255) DEFAULT NULL, ADD validated_service_address_normalized VARCHAR(255) DEFAULT NULL, ADD validated_service_address_recognized TINYINT(1) DEFAULT NULL, ADD validated_service_address_within_service_area TINYINT(1) DEFAULT NULL, ADD validated_service_address_latitude DOUBLE PRECISION DEFAULT NULL, ADD validated_service_address_longitude DOUBLE PRECISION DEFAULT NULL, ADD validated_service_address_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oil_service_chat_session DROP validated_service_address, DROP validated_service_address_normalized, DROP validated_service_address_recognized, DROP validated_service_address_within_service_area, DROP validated_service_address_latitude, DROP validated_service_address_longitude, DROP validated_service_address_at');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
