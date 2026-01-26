<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260125235926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_order_other_photo (order_id CHAR(36) NOT NULL, file_id CHAR(36) NOT NULL, INDEX IDX_1AE7976B8D9F6D38 (order_id), INDEX IDX_1AE7976B93CB796C (file_id), PRIMARY KEY (order_id, file_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_order_other_photo ADD CONSTRAINT FK_1AE7976B8D9F6D38 FOREIGN KEY (order_id) REFERENCES oil_service_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_order_other_photo ADD CONSTRAINT FK_1AE7976B93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_order ADD oil_change_vehicle_photo_id CHAR(36) DEFAULT NULL, ADD vin_photo_id CHAR(36) DEFAULT NULL, ADD old_oil_filter_photo_id CHAR(36) DEFAULT NULL, ADD old_oil_photo_id CHAR(36) DEFAULT NULL, ADD odometer_photo_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_order ADD CONSTRAINT FK_BDC9BDAE2A3D751C FOREIGN KEY (oil_change_vehicle_photo_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_order ADD CONSTRAINT FK_BDC9BDAEE2426347 FOREIGN KEY (vin_photo_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_order ADD CONSTRAINT FK_BDC9BDAE7F7AFF65 FOREIGN KEY (old_oil_filter_photo_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_order ADD CONSTRAINT FK_BDC9BDAEEB830DE1 FOREIGN KEY (old_oil_photo_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_order ADD CONSTRAINT FK_BDC9BDAEED918C31 FOREIGN KEY (odometer_photo_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_BDC9BDAE2A3D751C ON oil_service_order (oil_change_vehicle_photo_id)');
        $this->addSql('CREATE INDEX IDX_BDC9BDAEE2426347 ON oil_service_order (vin_photo_id)');
        $this->addSql('CREATE INDEX IDX_BDC9BDAE7F7AFF65 ON oil_service_order (old_oil_filter_photo_id)');
        $this->addSql('CREATE INDEX IDX_BDC9BDAEEB830DE1 ON oil_service_order (old_oil_photo_id)');
        $this->addSql('CREATE INDEX IDX_BDC9BDAEED918C31 ON oil_service_order (odometer_photo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_order_other_photo DROP FOREIGN KEY FK_1AE7976B8D9F6D38');
        $this->addSql('ALTER TABLE oil_service_order_other_photo DROP FOREIGN KEY FK_1AE7976B93CB796C');
        $this->addSql('DROP TABLE oil_service_order_other_photo');
        $this->addSql('ALTER TABLE oil_service_order DROP FOREIGN KEY FK_BDC9BDAE2A3D751C');
        $this->addSql('ALTER TABLE oil_service_order DROP FOREIGN KEY FK_BDC9BDAEE2426347');
        $this->addSql('ALTER TABLE oil_service_order DROP FOREIGN KEY FK_BDC9BDAE7F7AFF65');
        $this->addSql('ALTER TABLE oil_service_order DROP FOREIGN KEY FK_BDC9BDAEEB830DE1');
        $this->addSql('ALTER TABLE oil_service_order DROP FOREIGN KEY FK_BDC9BDAEED918C31');
        $this->addSql('DROP INDEX IDX_BDC9BDAE2A3D751C ON oil_service_order');
        $this->addSql('DROP INDEX IDX_BDC9BDAEE2426347 ON oil_service_order');
        $this->addSql('DROP INDEX IDX_BDC9BDAE7F7AFF65 ON oil_service_order');
        $this->addSql('DROP INDEX IDX_BDC9BDAEEB830DE1 ON oil_service_order');
        $this->addSql('DROP INDEX IDX_BDC9BDAEED918C31 ON oil_service_order');
        $this->addSql('ALTER TABLE oil_service_order DROP oil_change_vehicle_photo_id, DROP vin_photo_id, DROP old_oil_filter_photo_id, DROP old_oil_photo_id, DROP odometer_photo_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
