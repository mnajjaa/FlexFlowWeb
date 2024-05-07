<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322115419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, card_info VARCHAR(255) NOT NULL, mm INT NOT NULL, yy INT NOT NULL, cvc INT NOT NULL, total NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom_cour VARCHAR(255) NOT NULL, rating INT NOT NULL, liked TINYINT(1) NOT NULL, disliked TINYINT(1) NOT NULL, INDEX IDX_D8892622A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande MODIFY idCommande INT NOT NULL');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_1');
        $this->addSql('DROP INDEX id_user ON commande');
        $this->addSql('DROP INDEX `primary` ON commande');
        $this->addSql('ALTER TABLE commande ADD date_commande DATETIME NOT NULL, DROP dateCommande, CHANGE idCommande id INT AUTO_INCREMENT NOT NULL, CHANGE id_user id_produit INT NOT NULL, CHANGE statut nom VARCHAR(255) NOT NULL, CHANGE MontantTotal montant DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE commande ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE cours MODIFY id_cour INT NOT NULL');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY cours_ibfk_1');
        $this->addSql('DROP INDEX id_user ON cours');
        $this->addSql('DROP INDEX `primary` ON cours');
        $this->addSql('ALTER TABLE cours ADD user_id INT NOT NULL, ADD nom_cour VARCHAR(255) NOT NULL, ADD capacite INT NOT NULL, ADD image LONGBLOB NOT NULL, DROP id_user, DROP nomCour, DROP nbr_participants, CHANGE Duree duree VARCHAR(255) NOT NULL, CHANGE Intensite intensite VARCHAR(255) NOT NULL, CHANGE Cible cible VARCHAR(255) NOT NULL, CHANGE Categorie categorie VARCHAR(255) NOT NULL, CHANGE Objectif objectif VARCHAR(255) NOT NULL, CHANGE id_cour id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9CA76ED395 ON cours (user_id)');
        $this->addSql('ALTER TABLE cours ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE demande MODIFY id_demande INT NOT NULL');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY demande_ibfk_2');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY demande_ibfk_1');
        $this->addSql('DROP INDEX id_user ON demande');
        $this->addSql('DROP INDEX id_offre ON demande');
        $this->addSql('DROP INDEX `primary` ON demande');
        $this->addSql('ALTER TABLE demande ADD user_id INT NOT NULL, ADD offre_id INT NOT NULL, ADD niveau_physique VARCHAR(255) NOT NULL, ADD maladie_chronique VARCHAR(255) NOT NULL, DROP id_user, DROP id_offre, DROP NiveauPhysique, DROP MaladieChronique, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE But but VARCHAR(255) NOT NULL, CHANGE etat etat VARCHAR(255) NOT NULL, CHANGE lesjours lesjours VARCHAR(255) NOT NULL, CHANGE id_demande id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A54CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('CREATE INDEX IDX_2694D7A5A76ED395 ON demande (user_id)');
        $this->addSql('CREATE INDEX IDX_2694D7A54CC8505A ON demande (offre_id)');
        $this->addSql('ALTER TABLE demande ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE evaluations CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE note note INT NOT NULL');
        $this->addSql('ALTER TABLE evenement MODIFY id_evenement INT NOT NULL');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY evenement_ibfk_1');
        $this->addSql('DROP INDEX id_user ON evenement');
        $this->addSql('DROP INDEX `primary` ON evenement');
        $this->addSql('ALTER TABLE evenement ADD user_id INT NOT NULL, ADD nom_evenement VARCHAR(255) NOT NULL, ADD nbr_place INT NOT NULL, ADD time TIME NOT NULL, ADD image LONGBLOB NOT NULL, DROP id_user, DROP nomEvenement, DROP nbrPlace, DROP moderateur, CHANGE categorie categorie VARCHAR(255) NOT NULL, CHANGE Objectif objectif VARCHAR(255) NOT NULL, CHANGE etat etat INT NOT NULL, CHANGE id_evenement id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B26681EA76ED395 ON evenement (user_id)');
        $this->addSql('ALTER TABLE evenement ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY offre_ibfk_1');
        $this->addSql('DROP INDEX id_coach ON offre');
        $this->addSql('ALTER TABLE offre ADD etat_offre VARCHAR(255) DEFAULT \'En attente\' NOT NULL, ADD email VARCHAR(255) NOT NULL, DROP etatOffre, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE id_coach coach_id INT NOT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F3C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AF86866F3C105691 ON offre (coach_id)');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY participation_ibfk_1');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY participation_ibfk_2');
        $this->addSql('DROP INDEX id_cours ON participation');
        $this->addSql('DROP INDEX id_user ON participation');
        $this->addSql('ALTER TABLE participation ADD user_id INT NOT NULL, ADD nom_cour VARCHAR(255) NOT NULL, ADD nom_participant VARCHAR(255) NOT NULL, DROP id_user, DROP id_cours');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AB55E24FA76ED395 ON participation (user_id)');
        $this->addSql('ALTER TABLE produit MODIFY id-produit INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON produit');
        $this->addSql('ALTER TABLE produit CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE Description description VARCHAR(255) NOT NULL, CHANGE Prix prix DOUBLE PRECISION NOT NULL, CHANGE Type type VARCHAR(255) NOT NULL, CHANGE Quantite quantite INT NOT NULL, CHANGE image image LONGBLOB NOT NULL, CHANGE id-produit id INT AUTO_INCREMENT NOT NULL, CHANGE quantiteVendues quantite_vendues INT NOT NULL');
        $this->addSql('ALTER TABLE produit ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE reclamation MODIFY id_reclamation INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY reclamation_ibfk_1');
        $this->addSql('DROP INDEX id_user ON reclamation');
        $this->addSql('DROP INDEX `primary` ON reclamation');
        $this->addSql('ALTER TABLE reclamation ADD user_id INT DEFAULT NULL, DROP id_user, CHANGE description description VARCHAR(255) NOT NULL, CHANGE etat etat VARCHAR(255) NOT NULL, CHANGE id_reclamation id INT AUTO_INCREMENT NOT NULL, CHANGE date reclamation date_reclamation DATE NOT NULL, CHANGE sujet titre_reclamation VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE606404A76ED395 ON reclamation (user_id)');
        $this->addSql('ALTER TABLE reclamation ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY reponse_ibfk_1');
        $this->addSql('DROP INDEX id_reclamation ON reponse');
        $this->addSql('ALTER TABLE reponse DROP date_reponse, CHANGE id_reclamation reclamation_id INT NOT NULL, CHANGE reponse reponse_reclamation VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC72D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5FB6DEC72D6BA2D9 ON reponse (reclamation_id)');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY reservation_ibfk_1');
        $this->addSql('DROP INDEX id_user ON reservation');
        $this->addSql('ALTER TABLE reservation ADD user_id INT NOT NULL, ADD nom_evenement VARCHAR(255) NOT NULL, ADD nom_participant VARCHAR(255) NOT NULL, DROP id_user, DROP nbre_places, CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD is_verified TINYINT(1) NOT NULL, DROP nom, DROP telephone, DROP role, DROP etat, DROP image, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE commande MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON commande');
        $this->addSql('ALTER TABLE commande ADD dateCommande DATE NOT NULL, DROP date_commande, CHANGE id_produit id_user INT NOT NULL, CHANGE id idCommande INT AUTO_INCREMENT NOT NULL, CHANGE montant MontantTotal DOUBLE PRECISION NOT NULL, CHANGE nom statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT commande_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX id_user ON commande (id_user)');
        $this->addSql('ALTER TABLE commande ADD PRIMARY KEY (idCommande)');
        $this->addSql('ALTER TABLE cours MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CA76ED395');
        $this->addSql('DROP INDEX IDX_FDCA8C9CA76ED395 ON cours');
        $this->addSql('DROP INDEX `PRIMARY` ON cours');
        $this->addSql('ALTER TABLE cours ADD id_user INT NOT NULL, ADD nomCour VARCHAR(30) NOT NULL, ADD nbr_participants INT NOT NULL, DROP user_id, DROP nom_cour, DROP capacite, DROP image, CHANGE duree Duree VARCHAR(30) NOT NULL, CHANGE intensite Intensite VARCHAR(30) NOT NULL, CHANGE cible Cible VARCHAR(30) NOT NULL, CHANGE categorie Categorie VARCHAR(30) NOT NULL, CHANGE objectif Objectif VARCHAR(30) NOT NULL, CHANGE id id_cour INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT cours_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX id_user ON cours (id_user)');
        $this->addSql('ALTER TABLE cours ADD PRIMARY KEY (id_cour)');
        $this->addSql('ALTER TABLE demande MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5A76ED395');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A54CC8505A');
        $this->addSql('DROP INDEX IDX_2694D7A5A76ED395 ON demande');
        $this->addSql('DROP INDEX IDX_2694D7A54CC8505A ON demande');
        $this->addSql('DROP INDEX `PRIMARY` ON demande');
        $this->addSql('ALTER TABLE demande ADD id_user INT NOT NULL, ADD id_offre INT NOT NULL, ADD NiveauPhysique VARCHAR(30) NOT NULL, ADD MaladieChronique VARCHAR(500) NOT NULL, DROP user_id, DROP offre_id, DROP niveau_physique, DROP maladie_chronique, CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE but But VARCHAR(30) NOT NULL, CHANGE etat etat VARCHAR(50) NOT NULL, CHANGE lesjours lesjours VARCHAR(55) NOT NULL, CHANGE id id_demande INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT demande_ibfk_2 FOREIGN KEY (id_offre) REFERENCES offre (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT demande_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX id_user ON demande (id_user)');
        $this->addSql('CREATE INDEX id_offre ON demande (id_offre)');
        $this->addSql('ALTER TABLE demande ADD PRIMARY KEY (id_demande)');
        $this->addSql('ALTER TABLE evaluations CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE note note INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EA76ED395');
        $this->addSql('DROP INDEX IDX_B26681EA76ED395 ON evenement');
        $this->addSql('DROP INDEX `PRIMARY` ON evenement');
        $this->addSql('ALTER TABLE evenement ADD id_user INT NOT NULL, ADD nomEvenement VARCHAR(30) NOT NULL, ADD nbrPlace INT NOT NULL, ADD moderateur VARCHAR(30) NOT NULL, DROP user_id, DROP nom_evenement, DROP nbr_place, DROP time, DROP image, CHANGE categorie categorie VARCHAR(30) NOT NULL, CHANGE objectif Objectif VARCHAR(30) NOT NULL, CHANGE etat etat TINYINT(1) NOT NULL, CHANGE id id_evenement INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT evenement_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX id_user ON evenement (id_user)');
        $this->addSql('ALTER TABLE evenement ADD PRIMARY KEY (id_evenement)');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F3C105691');
        $this->addSql('DROP INDEX IDX_AF86866F3C105691 ON offre');
        $this->addSql('ALTER TABLE offre ADD etatOffre VARCHAR(50) NOT NULL, DROP etat_offre, DROP email, CHANGE nom nom TEXT NOT NULL, CHANGE coach_id id_coach INT NOT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT offre_ibfk_1 FOREIGN KEY (id_coach) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX id_coach ON offre (id_coach)');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FA76ED395');
        $this->addSql('DROP INDEX IDX_AB55E24FA76ED395 ON participation');
        $this->addSql('ALTER TABLE participation ADD id_cours INT NOT NULL, DROP nom_cour, DROP nom_participant, CHANGE user_id id_user INT NOT NULL');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT participation_ibfk_1 FOREIGN KEY (id_cours) REFERENCES cours (id_cour)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT participation_ibfk_2 FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX id_cours ON participation (id_cours)');
        $this->addSql('CREATE INDEX id_user ON participation (id_user)');
        $this->addSql('ALTER TABLE produit MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON produit');
        $this->addSql('ALTER TABLE produit CHANGE nom nom VARCHAR(30) NOT NULL, CHANGE description Description VARCHAR(30) NOT NULL, CHANGE prix Prix INT NOT NULL, CHANGE type Type VARCHAR(30) NOT NULL, CHANGE quantite Quantite VARCHAR(30) NOT NULL, CHANGE image image BLOB NOT NULL, CHANGE id id-produit INT AUTO_INCREMENT NOT NULL, CHANGE quantite_vendues quantiteVendues INT NOT NULL');
        $this->addSql('ALTER TABLE produit ADD PRIMARY KEY (id-produit)');
        $this->addSql('ALTER TABLE reclamation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('DROP INDEX IDX_CE606404A76ED395 ON reclamation');
        $this->addSql('DROP INDEX `PRIMARY` ON reclamation');
        $this->addSql('ALTER TABLE reclamation ADD id_user INT NOT NULL, DROP user_id, CHANGE description description VARCHAR(30) NOT NULL, CHANGE etat etat VARCHAR(30) NOT NULL, CHANGE id id_reclamation INT AUTO_INCREMENT NOT NULL, CHANGE titre_reclamation sujet VARCHAR(255) NOT NULL, CHANGE date_reclamation date reclamation DATE NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT reclamation_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX id_user ON reclamation (id_user)');
        $this->addSql('ALTER TABLE reclamation ADD PRIMARY KEY (id_reclamation)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC72D6BA2D9');
        $this->addSql('DROP INDEX UNIQ_5FB6DEC72D6BA2D9 ON reponse');
        $this->addSql('ALTER TABLE reponse ADD date_reponse DATE NOT NULL, CHANGE reclamation_id id_reclamation INT NOT NULL, CHANGE reponse_reclamation reponse VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT reponse_ibfk_1 FOREIGN KEY (id_reclamation) REFERENCES reclamation (id_reclamation)');
        $this->addSql('CREATE INDEX id_reclamation ON reponse (id_reclamation)');
        $this->addSql('ALTER TABLE reservation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('DROP INDEX IDX_42C84955A76ED395 ON reservation');
        $this->addSql('DROP INDEX `primary` ON reservation');
        $this->addSql('ALTER TABLE reservation ADD nbre_places INT NOT NULL, DROP nom_evenement, DROP nom_participant, CHANGE id id INT NOT NULL, CHANGE user_id id_user INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT reservation_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX id_user ON reservation (id_user)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) NOT NULL, ADD telephone VARCHAR(255) NOT NULL, ADD role VARCHAR(255) NOT NULL, ADD etat TINYINT(1) DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, DROP roles, DROP is_verified, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
