<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503194823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE login_history (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, login_date DATE NOT NULL, ip_adress VARCHAR(255) NOT NULL, navigateur VARCHAR(255) DEFAULT NULL, sys_exp VARCHAR(255) NOT NULL, INDEX IDX_37976E36A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE login_history ADD CONSTRAINT FK_37976E36A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande CHANGE nom_user nom_user VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE produit CHANGE prix prix INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD mfa_secret VARCHAR(255) DEFAULT NULL, ADD mfa_enabled TINYINT(1) DEFAULT NULL, ADD mdp_exp DATE DEFAULT NULL, ADD created_at DATE DEFAULT NULL, CHANGE email email VARCHAR(180) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_history DROP FOREIGN KEY FK_37976E36A76ED395');
        $this->addSql('DROP TABLE login_history');
        $this->addSql('ALTER TABLE commande CHANGE nom_user nom_user VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE user DROP mfa_secret, DROP mfa_enabled, DROP mdp_exp, DROP created_at, CHANGE email email VARCHAR(180) NOT NULL');
    }
}
