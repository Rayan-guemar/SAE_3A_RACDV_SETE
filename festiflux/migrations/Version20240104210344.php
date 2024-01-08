<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240104210344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_validation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE poste_utilisateur DROP FOREIGN KEY FK_D5BA2797A0905086');
        $this->addSql('ALTER TABLE poste_utilisateur DROP FOREIGN KEY FK_D5BA2797FB88E14F');
        $this->addSql('DROP TABLE poste_utilisateur');
        $this->addSql('ALTER TABLE festival ADD demande_validation_id INT DEFAULT NULL, ADD open TINYINT(1) NOT NULL, ADD valid TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE festival ADD CONSTRAINT FK_57CF78912A019E0 FOREIGN KEY (demande_validation_id) REFERENCES demande_validation (id)');
        $this->addSql('CREATE INDEX IDX_57CF78912A019E0 ON festival (demande_validation_id)');
        $this->addSql('ALTER TABLE utilisateur CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE festival DROP FOREIGN KEY FK_57CF78912A019E0');
        $this->addSql('CREATE TABLE poste_utilisateur (poste_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_D5BA2797A0905086 (poste_id), INDEX IDX_D5BA2797FB88E14F (utilisateur_id), PRIMARY KEY(poste_id, utilisateur_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE poste_utilisateur ADD CONSTRAINT FK_D5BA2797A0905086 FOREIGN KEY (poste_id) REFERENCES poste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE poste_utilisateur ADD CONSTRAINT FK_D5BA2797FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE demande_validation');
        $this->addSql('DROP INDEX IDX_57CF78912A019E0 ON festival');
        $this->addSql('ALTER TABLE festival DROP demande_validation_id, DROP open, DROP valid');
        $this->addSql('ALTER TABLE utilisateur CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) NOT NULL');
    }
}
