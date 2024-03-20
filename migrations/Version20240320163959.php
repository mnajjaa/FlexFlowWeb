<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320163959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD date_commande DATETIME NOT NULL, ADD id_produit INT NOT NULL, ADD nom VARCHAR(255) NOT NULL, ADD montant DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD name VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD card_info VARCHAR(255) NOT NULL, ADD mm INT NOT NULL, ADD yy INT NOT NULL, ADD cvc INT NOT NULL, ADD total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD nom VARCHAR(255) NOT NULL, ADD description VARCHAR(255) NOT NULL, ADD prix DOUBLE PRECISION NOT NULL, ADD type VARCHAR(255) NOT NULL, ADD quantite INT NOT NULL, ADD quantite_vendues INT NOT NULL, ADD image LONGBLOB NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP date_commande, DROP id_produit, DROP nom, DROP montant');
        $this->addSql('ALTER TABLE paiement DROP name, DROP email, DROP card_info, DROP mm, DROP yy, DROP cvc, DROP total');
        $this->addSql('ALTER TABLE produit DROP nom, DROP description, DROP prix, DROP type, DROP quantite, DROP quantite_vendues, DROP image');
    }
}
