<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009223802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D38AEBAF57');
        $this->addSql('DROP INDEX IDX_F4DD61D38AEBAF57 ON affectation');
        $this->addSql('DROP INDEX `primary` ON affectation');
        $this->addSql('ALTER TABLE affectation CHANGE festival_id crenaux_id INT NOT NULL');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3819C8220 FOREIGN KEY (crenaux_id) REFERENCES festival (id)');
        $this->addSql('CREATE INDEX IDX_F4DD61D3819C8220 ON affectation (crenaux_id)');
        $this->addSql('ALTER TABLE affectation ADD PRIMARY KEY (utilisateur_id, crenaux_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3819C8220');
        $this->addSql('DROP INDEX IDX_F4DD61D3819C8220 ON affectation');
        $this->addSql('DROP INDEX `PRIMARY` ON affectation');
        $this->addSql('ALTER TABLE affectation CHANGE crenaux_id festival_id INT NOT NULL');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D38AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)');
        $this->addSql('CREATE INDEX IDX_F4DD61D38AEBAF57 ON affectation (festival_id)');
        $this->addSql('ALTER TABLE affectation ADD PRIMARY KEY (utilisateur_id, festival_id)');
    }
}
