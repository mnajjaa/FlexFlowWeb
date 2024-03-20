<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320162625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD user_id INT NOT NULL, ADD nom_evenement VARCHAR(255) NOT NULL, ADD date DATE NOT NULL, ADD nbr_place INT NOT NULL, ADD categorie VARCHAR(255) NOT NULL, ADD objectif VARCHAR(255) NOT NULL, ADD etat INT NOT NULL, ADD time TIME NOT NULL, ADD image LONGBLOB NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B26681EA76ED395 ON evenement (user_id)');
        $this->addSql('ALTER TABLE reservation ADD user_id INT NOT NULL, ADD date_reservation DATE NOT NULL, ADD nom_evenement VARCHAR(255) NOT NULL, ADD nom_participant VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EA76ED395');
        $this->addSql('DROP INDEX IDX_B26681EA76ED395 ON evenement');
        $this->addSql('ALTER TABLE evenement DROP user_id, DROP nom_evenement, DROP date, DROP nbr_place, DROP categorie, DROP objectif, DROP etat, DROP time, DROP image');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('DROP INDEX IDX_42C84955A76ED395 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP user_id, DROP date_reservation, DROP nom_evenement, DROP nom_participant');
    }
}
