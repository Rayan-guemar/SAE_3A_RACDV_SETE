<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009223707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affectation (utilisateur_id INT NOT NULL, festival_id INT NOT NULL, INDEX IDX_F4DD61D3FB88E14F (utilisateur_id), INDEX IDX_F4DD61D38AEBAF57 (festival_id), PRIMARY KEY(utilisateur_id, festival_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D38AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE utilisateur_creneaux DROP FOREIGN KEY FK_698AC5F29F072641');
        $this->addSql('ALTER TABLE utilisateur_creneaux DROP FOREIGN KEY FK_698AC5F2FB88E14F');
        $this->addSql('DROP TABLE utilisateur_creneaux');
        $this->addSql('ALTER TABLE demandes_benevole DROP FOREIGN KEY FK_BA8C13308AEBAF57');
        $this->addSql('ALTER TABLE demandes_benevole ADD CONSTRAINT FK_BA8C13308AEBAF57 FOREIGN KEY (festival_id) REFERENCES creneaux (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur_creneaux (utilisateur_id INT NOT NULL, creneaux_id INT NOT NULL, INDEX IDX_698AC5F2FB88E14F (utilisateur_id), INDEX IDX_698AC5F29F072641 (creneaux_id), PRIMARY KEY(utilisateur_id, creneaux_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE utilisateur_creneaux ADD CONSTRAINT FK_698AC5F29F072641 FOREIGN KEY (creneaux_id) REFERENCES creneaux (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_creneaux ADD CONSTRAINT FK_698AC5F2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3FB88E14F');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D38AEBAF57');
        $this->addSql('DROP TABLE affectation');
        $this->addSql('ALTER TABLE demandes_benevole DROP FOREIGN KEY FK_BA8C13308AEBAF57');
        $this->addSql('ALTER TABLE demandes_benevole ADD CONSTRAINT FK_BA8C13308AEBAF57 FOREIGN KEY (festival_id) REFERENCES utilisateur (id)');
    }
}
