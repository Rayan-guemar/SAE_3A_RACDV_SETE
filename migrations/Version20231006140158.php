<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231006140158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur_creneaux (utilisateur_id INT NOT NULL, creneaux_id INT NOT NULL, INDEX IDX_698AC5F2FB88E14F (utilisateur_id), INDEX IDX_698AC5F29F072641 (creneaux_id), PRIMARY KEY(utilisateur_id, creneaux_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE utilisateur_creneaux ADD CONSTRAINT FK_698AC5F2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_creneaux ADD CONSTRAINT FK_698AC5F29F072641 FOREIGN KEY (creneaux_id) REFERENCES creneaux (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE creneaux ADD utilisateur_disponible_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE creneaux ADD CONSTRAINT FK_77F13C6D15029AFF FOREIGN KEY (utilisateur_disponible_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_77F13C6D15029AFF ON creneaux (utilisateur_disponible_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur_creneaux DROP FOREIGN KEY FK_698AC5F2FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_creneaux DROP FOREIGN KEY FK_698AC5F29F072641');
        $this->addSql('DROP TABLE utilisateur_creneaux');
        $this->addSql('ALTER TABLE creneaux DROP FOREIGN KEY FK_77F13C6D15029AFF');
        $this->addSql('DROP INDEX IDX_77F13C6D15029AFF ON creneaux');
        $this->addSql('ALTER TABLE creneaux DROP utilisateur_disponible_id');
    }
}
