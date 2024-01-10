<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240110155721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_postulation DROP FOREIGN KEY FK_3D340FD9B12C2DA');
        $this->addSql('DROP INDEX IDX_3D340FD9B12C2DA ON historique_postulation');
        $this->addSql('ALTER TABLE historique_postulation CHANGE id_fastival_id festival_id INT NOT NULL');
        $this->addSql('ALTER TABLE historique_postulation ADD CONSTRAINT FK_3D340FD8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('CREATE INDEX IDX_3D340FD8AEBAF57 ON historique_postulation (festival_id)');
        $this->addSql('ALTER TABLE validation ADD motif VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE validation DROP motif');
        $this->addSql('ALTER TABLE historique_postulation DROP FOREIGN KEY FK_3D340FD8AEBAF57');
        $this->addSql('DROP INDEX IDX_3D340FD8AEBAF57 ON historique_postulation');
        $this->addSql('ALTER TABLE historique_postulation CHANGE festival_id id_fastival_id INT NOT NULL');
        $this->addSql('ALTER TABLE historique_postulation ADD CONSTRAINT FK_3D340FD9B12C2DA FOREIGN KEY (id_fastival_id) REFERENCES festival (id)');
        $this->addSql('CREATE INDEX IDX_3D340FD9B12C2DA ON historique_postulation (id_fastival_id)');
    }
}
