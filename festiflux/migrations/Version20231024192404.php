<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231024192404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE creneaux (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, utilisateur_disponible_id INT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_77F13C6D8AEBAF57 (festival_id), INDEX IDX_77F13C6D15029AFF (utilisateur_disponible_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poste (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_7C890FAB8AEBAF57 (festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE creneaux ADD CONSTRAINT FK_77F13C6D8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE creneaux ADD CONSTRAINT FK_77F13C6D15029AFF FOREIGN KEY (utilisateur_disponible_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FAB8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE disponibilite ADD CONSTRAINT FK_2CBACE2F7D0729A9 FOREIGN KEY (creneau_id) REFERENCES creneaux (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720756AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075819C8220 FOREIGN KEY (crenaux_id) REFERENCES creneaux (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE disponibilite DROP FOREIGN KEY FK_2CBACE2F7D0729A9');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075819C8220');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075A0905086');
        $this->addSql('ALTER TABLE creneaux DROP FOREIGN KEY FK_77F13C6D8AEBAF57');
        $this->addSql('ALTER TABLE creneaux DROP FOREIGN KEY FK_77F13C6D15029AFF');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FAB8AEBAF57');
        $this->addSql('DROP TABLE creneaux');
        $this->addSql('DROP TABLE poste');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720756AB213CC');
    }
}
