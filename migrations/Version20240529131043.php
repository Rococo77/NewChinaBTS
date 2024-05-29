<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240529131043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allergen (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur_ingredient (fournisseur_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_568A27EB670C757F (fournisseur_id), INDEX IDX_568A27EB933FE08C (ingredient_id), PRIMARY KEY(fournisseur_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fournisseur_ingredient ADD CONSTRAINT FK_568A27EB670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fournisseur_ingredient ADD CONSTRAINT FK_568A27EB933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_user DROP FOREIGN KEY FK_E6FFD7AA82EA2E54');
        $this->addSql('ALTER TABLE commande_user DROP FOREIGN KEY FK_E6FFD7AAA76ED395');
        $this->addSql('DROP TABLE commande_user');
        $this->addSql('ALTER TABLE commande ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('ALTER TABLE ingredient ADD label_id INT DEFAULT NULL, ADD allergen TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF787033B92F39 FOREIGN KEY (label_id) REFERENCES label (id)');
        $this->addSql('CREATE INDEX IDX_6BAF787033B92F39 ON ingredient (label_id)');
        $this->addSql('ALTER TABLE plat DROP allergen, DROP img_url');
        $this->addSql('ALTER TABLE region DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF787033B92F39');
        $this->addSql('CREATE TABLE commande_user (commande_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E6FFD7AAA76ED395 (user_id), INDEX IDX_E6FFD7AA82EA2E54 (commande_id), PRIMARY KEY(commande_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande_user ADD CONSTRAINT FK_E6FFD7AA82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_user ADD CONSTRAINT FK_E6FFD7AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fournisseur_ingredient DROP FOREIGN KEY FK_568A27EB670C757F');
        $this->addSql('ALTER TABLE fournisseur_ingredient DROP FOREIGN KEY FK_568A27EB933FE08C');
        $this->addSql('DROP TABLE allergen');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE fournisseur_ingredient');
        $this->addSql('DROP TABLE label');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('ALTER TABLE commande DROP user_id');
        $this->addSql('DROP INDEX IDX_6BAF787033B92F39 ON ingredient');
        $this->addSql('ALTER TABLE ingredient DROP label_id, DROP allergen');
        $this->addSql('ALTER TABLE plat ADD allergen VARCHAR(255) DEFAULT NULL, ADD img_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE region ADD description VARCHAR(255) DEFAULT NULL');
    }
}
