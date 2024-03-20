<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320161227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande ADD user_id INT NOT NULL, ADD offre_id INT NOT NULL, ADD nom VARCHAR(255) NOT NULL, ADD age INT NOT NULL, ADD but VARCHAR(255) NOT NULL, ADD niveau_physique VARCHAR(255) NOT NULL, ADD maladie_chronique VARCHAR(255) NOT NULL, ADD nombreheure INT NOT NULL, ADD etat VARCHAR(255) NOT NULL, ADD horaire TIME NOT NULL, ADD lesjours VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A54CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('CREATE INDEX IDX_2694D7A5A76ED395 ON demande (user_id)');
        $this->addSql('CREATE INDEX IDX_2694D7A54CC8505A ON demande (offre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5A76ED395');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A54CC8505A');
        $this->addSql('DROP INDEX IDX_2694D7A5A76ED395 ON demande');
        $this->addSql('DROP INDEX IDX_2694D7A54CC8505A ON demande');
        $this->addSql('ALTER TABLE demande DROP user_id, DROP offre_id, DROP nom, DROP age, DROP but, DROP niveau_physique, DROP maladie_chronique, DROP nombreheure, DROP etat, DROP horaire, DROP lesjours');
    }
}
