<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231002203658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP INDEX UNIQ_57CF78930687172 ON festival');
        $this->addSql('ALTER TABLE festival ADD organisateur_id INT NOT NULL, ADD lieu VARCHAR(255) NOT NULL, ADD affiche VARCHAR(255) NOT NULL, DROP id_organisateur_id, DROP id_festival, CHANGE date_debut date_debut DATETIME NOT NULL, CHANGE date_fin date_fin DATETIME NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE nom_festival nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE festival ADD CONSTRAINT FK_57CF789D936B2FA FOREIGN KEY (organisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_57CF789D936B2FA ON festival (organisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE festival DROP FOREIGN KEY FK_57CF789D936B2FA');
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, id_administrateur INT NOT NULL, email VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, nom VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, mot_de_passe VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP INDEX IDX_57CF789D936B2FA ON festival');
        $this->addSql('ALTER TABLE festival ADD id_festival INT NOT NULL, ADD nom_festival VARCHAR(255) NOT NULL, DROP nom, DROP lieu, DROP affiche, CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE organisateur_id id_organisateur_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57CF78930687172 ON festival (id_organisateur_id)');
    }
}
