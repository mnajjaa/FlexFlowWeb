<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320160417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F6CCBBA04');
        $this->addSql('DROP INDEX IDX_AF86866F6CCBBA04 ON offre');
        $this->addSql('ALTER TABLE offre CHANGE id_coach_id coach_id INT NOT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F3C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AF86866F3C105691 ON offre (coach_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F3C105691');
        $this->addSql('DROP INDEX IDX_AF86866F3C105691 ON offre');
        $this->addSql('ALTER TABLE offre CHANGE coach_id id_coach_id INT NOT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F6CCBBA04 FOREIGN KEY (id_coach_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AF86866F6CCBBA04 ON offre (id_coach_id)');
    }
}
