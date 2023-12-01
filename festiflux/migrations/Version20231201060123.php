<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201060123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE poste ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE utilisateur ALTER adresse DROP NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ALTER description DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE poste_id_seq');
        $this->addSql('SELECT setval(\'poste_id_seq\', (SELECT MAX(id) FROM poste))');
        $this->addSql('ALTER TABLE poste ALTER id SET DEFAULT nextval(\'poste_id_seq\')');
        $this->addSql('ALTER TABLE utilisateur ALTER adresse SET NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ALTER description SET NOT NULL');
    }
}
