<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927115059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, id_administrateur INT NOT NULL, email VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE festival (id INT AUTO_INCREMENT NOT NULL, id_organisateur_id INT NOT NULL, id_festival INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, nom_festival VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_57CF78930687172 (id_organisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE festival ADD CONSTRAINT FK_57CF78930687172 FOREIGN KEY (id_organisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3AA08CB10 ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B388D20D42 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur ADD id_utilisateur INT NOT NULL, ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD mot_de_passe VARCHAR(255) NOT NULL, DROP login, DROP roles, DROP password, DROP adresse_email, DROP nom_photo_profil');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_publication DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE festival DROP FOREIGN KEY FK_57CF78930687172');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE festival');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE utilisateur ADD login VARCHAR(180) NOT NULL, ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD password VARCHAR(255) NOT NULL, ADD adresse_email VARCHAR(255) NOT NULL, ADD nom_photo_profil LONGTEXT DEFAULT NULL, DROP id_utilisateur, DROP nom, DROP prenom, DROP email, DROP mot_de_passe');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3AA08CB10 ON utilisateur (login)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B388D20D42 ON utilisateur (adresse_email)');
    }
}
