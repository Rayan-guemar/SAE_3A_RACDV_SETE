<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026100506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075A0905086');
        $this->addSql('CREATE TABLE tache_utilisateur (tache_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_8E15C4FDD2235D39 (tache_id), INDEX IDX_8E15C4FDFB88E14F (utilisateur_id), PRIMARY KEY(tache_id, utilisateur_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tache_utilisateur ADD CONSTRAINT FK_8E15C4FDD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tache_utilisateur ADD CONSTRAINT FK_8E15C4FDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE disponibilite DROP FOREIGN KEY FK_2CBACE2FFB88E14F');
        $this->addSql('ALTER TABLE disponibilite DROP FOREIGN KEY FK_2CBACE2F7D0729A9');
        $this->addSql('ALTER TABLE disponibilite DROP FOREIGN KEY FK_2CBACE2F8AEBAF57');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FAB8AEBAF57');
        $this->addSql('DROP TABLE disponibilite');
        $this->addSql('DROP TABLE poste');
        $this->addSql('ALTER TABLE creneaux ADD festival_id INT NOT NULL, ADD utilisateur_disponible_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE creneaux ADD CONSTRAINT FK_77F13C6D8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE creneaux ADD CONSTRAINT FK_77F13C6D15029AFF FOREIGN KEY (utilisateur_disponible_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_77F13C6D8AEBAF57 ON creneaux (festival_id)');
        $this->addSql('CREATE INDEX IDX_77F13C6D15029AFF ON creneaux (utilisateur_disponible_id)');
        $this->addSql('DROP INDEX IDX_93872075A0905086 ON tache');
        $this->addSql('ALTER TABLE tache ADD festival_id INT NOT NULL, DROP poste_id, DROP description, DROP nombre_benevole');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720758AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('CREATE INDEX IDX_938720758AEBAF57 ON tache (festival_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE disponibilite (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, festival_id INT NOT NULL, creneau_id INT NOT NULL, INDEX IDX_2CBACE2F7D0729A9 (creneau_id), INDEX IDX_2CBACE2FFB88E14F (utilisateur_id), INDEX IDX_2CBACE2F8AEBAF57 (festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE poste (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX IDX_7C890FAB8AEBAF57 (festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE disponibilite ADD CONSTRAINT FK_2CBACE2FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE disponibilite ADD CONSTRAINT FK_2CBACE2F7D0729A9 FOREIGN KEY (creneau_id) REFERENCES creneaux (id)');
        $this->addSql('ALTER TABLE disponibilite ADD CONSTRAINT FK_2CBACE2F8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FAB8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE tache_utilisateur DROP FOREIGN KEY FK_8E15C4FDD2235D39');
        $this->addSql('ALTER TABLE tache_utilisateur DROP FOREIGN KEY FK_8E15C4FDFB88E14F');
        $this->addSql('DROP TABLE tache_utilisateur');
        $this->addSql('ALTER TABLE creneaux DROP FOREIGN KEY FK_77F13C6D8AEBAF57');
        $this->addSql('ALTER TABLE creneaux DROP FOREIGN KEY FK_77F13C6D15029AFF');
        $this->addSql('DROP INDEX IDX_77F13C6D8AEBAF57 ON creneaux');
        $this->addSql('DROP INDEX IDX_77F13C6D15029AFF ON creneaux');
        $this->addSql('ALTER TABLE creneaux DROP festival_id, DROP utilisateur_disponible_id');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720758AEBAF57');
        $this->addSql('DROP INDEX IDX_938720758AEBAF57 ON tache');
        $this->addSql('ALTER TABLE tache ADD description VARCHAR(255) NOT NULL, ADD nombre_benevole INT NOT NULL, CHANGE festival_id poste_id INT NOT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        $this->addSql('CREATE INDEX IDX_93872075A0905086 ON tache (poste_id)');
    }
}
