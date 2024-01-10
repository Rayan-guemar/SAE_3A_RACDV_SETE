<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240110160005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_postulation DROP FOREIGN KEY FK_3D340FDC6EE5C49');
        $this->addSql('DROP INDEX IDX_3D340FDC6EE5C49 ON historique_postulation');
        $this->addSql('ALTER TABLE historique_postulation CHANGE id_utilisateur_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE historique_postulation ADD CONSTRAINT FK_3D340FDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_3D340FDFB88E14F ON historique_postulation (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_postulation DROP FOREIGN KEY FK_3D340FDFB88E14F');
        $this->addSql('DROP INDEX IDX_3D340FDFB88E14F ON historique_postulation');
        $this->addSql('ALTER TABLE historique_postulation CHANGE utilisateur_id id_utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE historique_postulation ADD CONSTRAINT FK_3D340FDC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_3D340FDC6EE5C49 ON historique_postulation (id_utilisateur_id)');
    }
}
