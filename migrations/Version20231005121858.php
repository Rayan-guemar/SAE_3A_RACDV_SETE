<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005121858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE creneaux (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_77F13C6D8AEBAF57 (festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, nom_lieu VARCHAR(255) NOT NULL, INDEX IDX_2F577D598AEBAF57 (festival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tache (id INT AUTO_INCREMENT NOT NULL, festival_id INT NOT NULL, lieu_id INT DEFAULT NULL, INDEX IDX_938720758AEBAF57 (festival_id), INDEX IDX_938720756AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE creneaux ADD CONSTRAINT FK_77F13C6D8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D598AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720758AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720756AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creneaux DROP FOREIGN KEY FK_77F13C6D8AEBAF57');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D598AEBAF57');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720758AEBAF57');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720756AB213CC');
        $this->addSql('DROP TABLE creneaux');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE tache');
    }
}
