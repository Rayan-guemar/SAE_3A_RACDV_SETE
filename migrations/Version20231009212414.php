<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009212414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_benevole (id INT AUTO_INCREMENT NOT NULL, festival_id_id INT NOT NULL, utilisateur_id_id INT DEFAULT NULL, INDEX IDX_F258061911F56659 (festival_id_id), INDEX IDX_F2580619B981C689 (utilisateur_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande_benevole ADD CONSTRAINT FK_F258061911F56659 FOREIGN KEY (festival_id_id) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE demande_benevole ADD CONSTRAINT FK_F2580619B981C689 FOREIGN KEY (utilisateur_id_id) REFERENCES utilisateur (id)');
        $this->addSql('DROP TABLE demandes_benevole');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demandes_benevole (utilisateur_id INT NOT NULL, festival_id INT NOT NULL, INDEX IDX_BA8C13308AEBAF57 (festival_id), INDEX IDX_BA8C1330FB88E14F (utilisateur_id), PRIMARY KEY(utilisateur_id, festival_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE demande_benevole DROP FOREIGN KEY FK_F258061911F56659');
        $this->addSql('ALTER TABLE demande_benevole DROP FOREIGN KEY FK_F2580619B981C689');
        $this->addSql('DROP TABLE demande_benevole');
    }
}
