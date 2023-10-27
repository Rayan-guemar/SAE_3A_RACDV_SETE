<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231027161015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache_utilisateur DROP FOREIGN KEY FK_8E15C4FDD2235D39');
        $this->addSql('ALTER TABLE tache_utilisateur DROP FOREIGN KEY FK_8E15C4FDFB88E14F');
        $this->addSql('DROP TABLE tache_utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tache_utilisateur (tache_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_8E15C4FDD2235D39 (tache_id), INDEX IDX_8E15C4FDFB88E14F (utilisateur_id), PRIMARY KEY(tache_id, utilisateur_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tache_utilisateur ADD CONSTRAINT FK_8E15C4FDD2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tache_utilisateur ADD CONSTRAINT FK_8E15C4FDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }
}
