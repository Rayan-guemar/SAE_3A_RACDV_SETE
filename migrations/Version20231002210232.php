<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231002210232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_festival (id INT AUTO_INCREMENT NOT NULL, organisateur_festival_id INT NOT NULL, nom_festival VARCHAR(255) NOT NULL, date_debut_festival DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin_festival DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description_festival LONGTEXT NOT NULL, lieu_festival VARCHAR(255) NOT NULL, affiche_festival VARCHAR(255) NOT NULL, INDEX IDX_4325BE4B9D7943F3 (organisateur_festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande_festival ADD CONSTRAINT FK_4325BE4B9D7943F3 FOREIGN KEY (organisateur_festival_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_festival DROP FOREIGN KEY FK_4325BE4B9D7943F3');
        $this->addSql('DROP TABLE demande_festival');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json)\'');
    }
}
