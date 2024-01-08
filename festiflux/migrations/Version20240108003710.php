<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240108003710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE validation (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, INDEX IDX_16AC5B6E8AEBAF57 (festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE validation ADD CONSTRAINT FK_16AC5B6E8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('DROP TABLE demande_validation');
        $this->addSql('DROP TABLE demande_benevole');
        $this->addSql('ALTER TABLE festival ADD open TINYINT(1) NOT NULL, ADD valid TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_validation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE demande_benevole (id INT AUTO_INCREMENT NOT NULL, statut TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE validation DROP FOREIGN KEY FK_16AC5B6E8AEBAF57');
        $this->addSql('DROP TABLE validation');
        $this->addSql('ALTER TABLE festival DROP open, DROP valid');
    }
}
