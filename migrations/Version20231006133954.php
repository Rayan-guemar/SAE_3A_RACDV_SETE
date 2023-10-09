<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231006133954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE est_benevole (utilisateur_id INT NOT NULL, festival_id INT NOT NULL, INDEX IDX_2F8ED272FB88E14F (utilisateur_id), INDEX IDX_2F8ED2728AEBAF57 (festival_id), PRIMARY KEY(utilisateur_id, festival_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE est_benevole ADD CONSTRAINT FK_2F8ED272FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE est_benevole ADD CONSTRAINT FK_2F8ED2728AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE est_benevole DROP FOREIGN KEY FK_2F8ED272FB88E14F');
        $this->addSql('ALTER TABLE est_benevole DROP FOREIGN KEY FK_2F8ED2728AEBAF57');
        $this->addSql('DROP TABLE est_benevole');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
