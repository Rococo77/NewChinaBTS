<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240420125154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier ADD plat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF2D73DB560 ON panier (plat_id)');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A207F77D927C');
        $this->addSql('DROP INDEX IDX_2038A207F77D927C ON plat');
        $this->addSql('ALTER TABLE plat DROP panier_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2D73DB560');
        $this->addSql('DROP INDEX IDX_24CC0DF2D73DB560 ON panier');
        $this->addSql('ALTER TABLE panier DROP plat_id');
        $this->addSql('ALTER TABLE plat ADD panier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A207F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('CREATE INDEX IDX_2038A207F77D927C ON plat (panier_id)');
    }
}
