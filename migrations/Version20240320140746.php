<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320140746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD user_id INT NOT NULL, ADD nom_cour VARCHAR(255) NOT NULL, ADD duree VARCHAR(255) NOT NULL, ADD intensite VARCHAR(255) NOT NULL, ADD cible VARCHAR(255) NOT NULL, ADD categorie VARCHAR(255) NOT NULL, ADD objectif VARCHAR(255) NOT NULL, ADD etat TINYINT(1) NOT NULL, ADD capacite INT NOT NULL, ADD image LONGBLOB NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9CA76ED395 ON cours (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CA76ED395');
        $this->addSql('DROP INDEX IDX_FDCA8C9CA76ED395 ON cours');
        $this->addSql('ALTER TABLE cours DROP user_id, DROP nom_cour, DROP duree, DROP intensite, DROP cible, DROP categorie, DROP objectif, DROP etat, DROP capacite, DROP image');
    }
}
