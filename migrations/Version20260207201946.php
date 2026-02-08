<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260207201946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oil_service_customer_car (id CHAR(36) NOT NULL, license_plate VARCHAR(20) NOT NULL, brand VARCHAR(64) DEFAULT NULL, model VARCHAR(255) DEFAULT NULL, vin VARCHAR(17) DEFAULT NULL, created_at DATETIME NOT NULL, dk_datum_prvni_registrace LONGTEXT DEFAULT NULL, dk_datum_prvni_registrace_vcr LONGTEXT DEFAULT NULL, dk_cislo_typoveho_schvaleni LONGTEXT DEFAULT NULL, dk_homologace_es LONGTEXT DEFAULT NULL, dk_vozidlo_druh LONGTEXT DEFAULT NULL, dk_vozidlo_druh2 LONGTEXT DEFAULT NULL, dk_kategorie LONGTEXT DEFAULT NULL, dk_tovarni_znacka LONGTEXT DEFAULT NULL, dk_typ LONGTEXT DEFAULT NULL, dk_varianta LONGTEXT DEFAULT NULL, dk_verze LONGTEXT DEFAULT NULL, dk_vin LONGTEXT DEFAULT NULL, dk_obchodni_oznaceni LONGTEXT DEFAULT NULL, dk_vozidlo_vyrobce LONGTEXT DEFAULT NULL, dk_motor_vyrobce LONGTEXT DEFAULT NULL, dk_motor_typ LONGTEXT DEFAULT NULL, dk_motor_max_vykon LONGTEXT DEFAULT NULL, dk_palivo LONGTEXT DEFAULT NULL, dk_motor_zdvih_objem LONGTEXT DEFAULT NULL, dk_vozidlo_elektricke LONGTEXT DEFAULT NULL, dk_vozidlo_hybridni LONGTEXT DEFAULT NULL, dk_vozidlo_hybridni_trida LONGTEXT DEFAULT NULL, dk_emise_ehkosnehses LONGTEXT DEFAULT NULL, dk_emisni_uroven LONGTEXT DEFAULT NULL, dk_emise_ksa LONGTEXT DEFAULT NULL, dk_emise_co2 LONGTEXT DEFAULT NULL, dk_emise_co2_specificke LONGTEXT DEFAULT NULL, dk_emise_snizeni_nedc LONGTEXT DEFAULT NULL, dk_emise_snizeni_wltp LONGTEXT DEFAULT NULL, dk_spotreba_metodika LONGTEXT DEFAULT NULL, dk_spotreba_na100_km LONGTEXT DEFAULT NULL, dk_spotreba LONGTEXT DEFAULT NULL, dk_spotreba_el LONGTEXT DEFAULT NULL, dk_dojezd_zr LONGTEXT DEFAULT NULL, dk_vyrobce_karoserie LONGTEXT DEFAULT NULL, dk_karoserie_druh LONGTEXT DEFAULT NULL, dk_karoserie_vyrobni_cislo LONGTEXT DEFAULT NULL, dk_vozidlo_karoserie_barva LONGTEXT DEFAULT NULL, dk_vozidlo_karoserie_barva_doplnkova LONGTEXT DEFAULT NULL, dk_vozidlo_karoserie_mist LONGTEXT DEFAULT NULL, dk_rozmery LONGTEXT DEFAULT NULL, dk_rozmery_rozvor LONGTEXT DEFAULT NULL, dk_rozchod LONGTEXT DEFAULT NULL, dk_hmotnosti_provozni LONGTEXT DEFAULT NULL, dk_hmotnosti_prip_pov LONGTEXT DEFAULT NULL, dk_hmotnosti_prip_pov_n LONGTEXT DEFAULT NULL, dk_hmotnosti_prip_pov_brzdene_pv LONGTEXT DEFAULT NULL, dk_hmotnosti_prip_pov_nebrzdene_pv LONGTEXT DEFAULT NULL, dk_hmotnosti_prip_pov_js LONGTEXT DEFAULT NULL, dk_hmotnosti_test_wltp LONGTEXT DEFAULT NULL, dk_hmotnost_uzitecne_zatizeni_prumer LONGTEXT DEFAULT NULL, dk_vozidlo_spoj_zariz_nazev LONGTEXT DEFAULT NULL, dk_napravy_pocet_druh LONGTEXT DEFAULT NULL, dk_napravy_pneu_rafky LONGTEXT DEFAULT NULL, dk_hluk_stojici_otacky LONGTEXT DEFAULT NULL, dk_hluk_jizda LONGTEXT DEFAULT NULL, dk_nejvyssi_rychlost LONGTEXT DEFAULT NULL, dk_pomer_vykon_hmotnost LONGTEXT DEFAULT NULL, dk_inovativni_technologie LONGTEXT DEFAULT NULL, dk_stupen_dokonceni LONGTEXT DEFAULT NULL, dk_faktor_odchylky_de LONGTEXT DEFAULT NULL, dk_faktor_verifikace_vf LONGTEXT DEFAULT NULL, dk_vozidlo_ucel LONGTEXT DEFAULT NULL, dk_dalsi_zaznamy LONGTEXT DEFAULT NULL, dk_alternativni_provedeni LONGTEXT DEFAULT NULL, dk_cislo_tp LONGTEXT DEFAULT NULL, dk_cislo_orv LONGTEXT DEFAULT NULL, dk_orv_zadrzeno LONGTEXT DEFAULT NULL, dk_orv_ke_skartaci LONGTEXT DEFAULT NULL, dk_orv_odevzdano LONGTEXT DEFAULT NULL, dk_rz_druh LONGTEXT DEFAULT NULL, dk_rz_jk_vydana LONGTEXT DEFAULT NULL, dk_rz_ke_skartaci LONGTEXT DEFAULT NULL, dk_rz_odevzdano LONGTEXT DEFAULT NULL, dk_rz_zadrzena LONGTEXT DEFAULT NULL, dk_zarazeni_vozidla LONGTEXT DEFAULT NULL, dk_pravidelna_technicka_prohlidka_do LONGTEXT DEFAULT NULL, dk_pred_registraci_prohlidka_dne LONGTEXT DEFAULT NULL, dk_pred_schvalenim_prohlidka_dne LONGTEXT DEFAULT NULL, dk_evidencni_prohlidka_dne LONGTEXT DEFAULT NULL, dk_historicke_vozidlo_prohlidka_dne LONGTEXT DEFAULT NULL, dk_status_nazev LONGTEXT DEFAULT NULL, dk_pocet_vlastniku LONGTEXT DEFAULT NULL, dk_pocet_provozovatelu LONGTEXT DEFAULT NULL, user_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_B8AFFB48F5AA79D0 (license_plate), UNIQUE INDEX UNIQ_B8AFFB48B1085141 (vin), INDEX IDX_B8AFFB48A76ED395 (user_id), INDEX idx_license_plate (license_plate), INDEX idx_vin (vin), INDEX idx_brand (brand), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oil_service_customer_car_history (id CHAR(36) NOT NULL, assigned_at DATETIME NOT NULL, car_id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL, INDEX idx_car (car_id), INDEX idx_user (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oil_service_customer_car ADD CONSTRAINT FK_B8AFFB48A76ED395 FOREIGN KEY (user_id) REFERENCES oil_service_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oil_service_customer_car_history ADD CONSTRAINT FK_70EC6A3C3C6F69F FOREIGN KEY (car_id) REFERENCES oil_service_customer_car (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_customer_car_history ADD CONSTRAINT FK_70EC6A3A76ED395 FOREIGN KEY (user_id) REFERENCES oil_service_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oil_service_order ADD vin VARCHAR(17) DEFAULT NULL, ADD customer_car_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE oil_service_order ADD CONSTRAINT FK_BDC9BDAE72032ACE FOREIGN KEY (customer_car_id) REFERENCES oil_service_customer_car (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_customer_car ON oil_service_order (customer_car_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oil_service_customer_car DROP FOREIGN KEY FK_B8AFFB48A76ED395');
        $this->addSql('ALTER TABLE oil_service_customer_car_history DROP FOREIGN KEY FK_70EC6A3C3C6F69F');
        $this->addSql('ALTER TABLE oil_service_customer_car_history DROP FOREIGN KEY FK_70EC6A3A76ED395');
        $this->addSql('DROP TABLE oil_service_customer_car');
        $this->addSql('DROP TABLE oil_service_customer_car_history');
        $this->addSql('ALTER TABLE oil_service_order DROP FOREIGN KEY FK_BDC9BDAE72032ACE');
        $this->addSql('DROP INDEX idx_customer_car ON oil_service_order');
        $this->addSql('ALTER TABLE oil_service_order DROP vin, DROP customer_car_id');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
