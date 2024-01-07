<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240107135502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE festival DROP FOREIGN KEY FK_57CF78912A019E0');
        $this->addSql('CREATE TABLE demande_benevole (id INT AUTO_INCREMENT NOT NULL, statut TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demandes_benevole (utilisateur_id INT NOT NULL, festival_id INT NOT NULL, INDEX IDX_BA8C1330FB88E14F (utilisateur_id), INDEX IDX_BA8C13308AEBAF57 (festival_id), PRIMARY KEY(utilisateur_id, festival_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demandes_benevole ADD CONSTRAINT FK_BA8C1330FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE demandes_benevole ADD CONSTRAINT FK_BA8C13308AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('DROP TABLE demande_validation');
        $this->addSql('DROP INDEX IDX_57CF78912A019E0 ON festival');
        $this->addSql('ALTER TABLE festival DROP demande_validation_id, DROP open, DROP valid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_validation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE demandes_benevole DROP FOREIGN KEY FK_BA8C1330FB88E14F');
        $this->addSql('ALTER TABLE demandes_benevole DROP FOREIGN KEY FK_BA8C13308AEBAF57');
        $this->addSql('DROP TABLE demande_benevole');
        $this->addSql('DROP TABLE demandes_benevole');
        $this->addSql('ALTER TABLE festival ADD demande_validation_id INT DEFAULT NULL, ADD open TINYINT(1) NOT NULL, ADD valid TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE festival ADD CONSTRAINT FK_57CF78912A019E0 FOREIGN KEY (demande_validation_id) REFERENCES demande_validation (id)');
        $this->addSql('CREATE INDEX IDX_57CF78912A019E0 ON festival (demande_validation_id)');
    }
}
