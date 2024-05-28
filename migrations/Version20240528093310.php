<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240528093310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_com DATETIME NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingr_stock (id INT AUTO_INCREMENT NOT NULL, peremption_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_plat (ingredient_id INT NOT NULL, plat_id INT NOT NULL, INDEX IDX_7E691291933FE08C (ingredient_id), INDEX IDX_7E691291D73DB560 (plat_id), PRIMARY KEY(ingredient_id, plat_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_ingr_stock (ingredient_id INT NOT NULL, ingr_stock_id INT NOT NULL, INDEX IDX_45A7F832933FE08C (ingredient_id), INDEX IDX_45A7F832926CFE2 (ingr_stock_id), PRIMARY KEY(ingredient_id, ingr_stock_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, INDEX IDX_24CC0DF267B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier_item (id INT AUTO_INCREMENT NOT NULL, panier_id INT NOT NULL, plat_id INT NOT NULL, commande_id INT DEFAULT NULL, quantité INT NOT NULL, INDEX IDX_EBFD0067F77D927C (panier_id), INDEX IDX_EBFD0067D73DB560 (plat_id), INDEX IDX_EBFD006782EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, prix_unit DOUBLE PRECISION NOT NULL, stock_qtt INT DEFAULT NULL, peremption_date DATE DEFAULT NULL, allergen VARCHAR(255) DEFAULT NULL, INDEX IDX_2038A20798260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, zip VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ingredient_plat ADD CONSTRAINT FK_7E691291933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredient_plat ADD CONSTRAINT FK_7E691291D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredient_ingr_stock ADD CONSTRAINT FK_45A7F832933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredient_ingr_stock ADD CONSTRAINT FK_45A7F832926CFE2 FOREIGN KEY (ingr_stock_id) REFERENCES ingr_stock (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF267B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier_item ADD CONSTRAINT FK_EBFD0067F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panier_item ADD CONSTRAINT FK_EBFD0067D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE panier_item ADD CONSTRAINT FK_EBFD006782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A20798260155 FOREIGN KEY (region_id) REFERENCES region (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE ingredient_plat DROP FOREIGN KEY FK_7E691291933FE08C');
        $this->addSql('ALTER TABLE ingredient_plat DROP FOREIGN KEY FK_7E691291D73DB560');
        $this->addSql('ALTER TABLE ingredient_ingr_stock DROP FOREIGN KEY FK_45A7F832933FE08C');
        $this->addSql('ALTER TABLE ingredient_ingr_stock DROP FOREIGN KEY FK_45A7F832926CFE2');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF267B3B43D');
        $this->addSql('ALTER TABLE panier_item DROP FOREIGN KEY FK_EBFD0067F77D927C');
        $this->addSql('ALTER TABLE panier_item DROP FOREIGN KEY FK_EBFD0067D73DB560');
        $this->addSql('ALTER TABLE panier_item DROP FOREIGN KEY FK_EBFD006782EA2E54');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A20798260155');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE ingr_stock');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE ingredient_plat');
        $this->addSql('DROP TABLE ingredient_ingr_stock');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_item');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE user');
    }
}
