<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240528011906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier_item DROP FOREIGN KEY FK_EBFD006782EA2E54');
        $this->addSql('DROP INDEX IDX_EBFD006782EA2E54 ON panier_item');
        $this->addSql('ALTER TABLE panier_item DROP commande_id');
        $this->addSql('ALTER TABLE plat ADD img_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier_item ADD commande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE panier_item ADD CONSTRAINT FK_EBFD006782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_EBFD006782EA2E54 ON panier_item (commande_id)');
        $this->addSql('ALTER TABLE plat DROP img_url');
    }
}
