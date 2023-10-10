<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231010001635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075D2235D39');
        $this->addSql('DROP INDEX UNIQ_93872075D2235D39 ON tache');
        $this->addSql('ALTER TABLE tache CHANGE tache_id crenaux_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075819C8220 FOREIGN KEY (crenaux_id) REFERENCES creneaux (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_93872075819C8220 ON tache (crenaux_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075819C8220');
        $this->addSql('DROP INDEX UNIQ_93872075819C8220 ON tache');
        $this->addSql('ALTER TABLE tache CHANGE crenaux_id tache_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075D2235D39 FOREIGN KEY (tache_id) REFERENCES creneaux (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_93872075D2235D39 ON tache (tache_id)');
    }
}
