<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231010152329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demandes_benevole DROP FOREIGN KEY FK_BA8C13308AEBAF57');
        $this->addSql('ALTER TABLE demandes_benevole ADD CONSTRAINT FK_BA8C13308AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3819C8220');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3819C8220 FOREIGN KEY (crenaux_id) REFERENCES creneaux (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3819C8220');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3819C8220 FOREIGN KEY (crenaux_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE demandes_benevole DROP FOREIGN KEY FK_BA8C13308AEBAF57');
        $this->addSql('ALTER TABLE demandes_benevole ADD CONSTRAINT FK_BA8C13308AEBAF57 FOREIGN KEY (festival_id) REFERENCES creneaux (id)');
    }
}
