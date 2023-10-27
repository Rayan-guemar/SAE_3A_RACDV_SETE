<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026222659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tache (id INT AUTO_INCREMENT NOT NULL, lieu_id INT DEFAULT NULL, crenaux_id INT DEFAULT NULL, poste_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, nombre_benevole INT NOT NULL, INDEX IDX_938720756AB213CC (lieu_id), UNIQUE INDEX UNIQ_93872075819C8220 (crenaux_id), INDEX IDX_93872075A0905086 (poste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tache_utilisateur (tache_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_8E15C4FDD2235D39 (tache_id), INDEX IDX_8E15C4FDFB88E14F (utilisateur_id), PRIMARY KEY(tache_id, utilisateur_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720756AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075819C8220 FOREIGN KEY (crenaux_id) REFERENCES creneaux (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE tache_utilisateur ADD CONSTRAINT FK_8E15C4FDD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tache_utilisateur ADD CONSTRAINT FK_8E15C4FDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720756AB213CC');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075819C8220');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075A0905086');
        $this->addSql('ALTER TABLE tache_utilisateur DROP FOREIGN KEY FK_8E15C4FDD2235D39');
        $this->addSql('ALTER TABLE tache_utilisateur DROP FOREIGN KEY FK_8E15C4FDFB88E14F');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE tache_utilisateur');
    }
}
