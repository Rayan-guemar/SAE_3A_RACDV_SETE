<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121162801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE festival_tag DROP FOREIGN KEY FK_9DD8F0E8BAD26311');
        $this->addSql('ALTER TABLE festival_tag DROP FOREIGN KEY FK_9DD8F0E88AEBAF57');
        $this->addSql('DROP TABLE festival_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('ALTER TABLE poste ADD couleur VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE festival_tag (festival_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_9DD8F0E8BAD26311 (tag_id), INDEX IDX_9DD8F0E88AEBAF57 (festival_id), PRIMARY KEY(festival_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE festival_tag ADD CONSTRAINT FK_9DD8F0E8BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE festival_tag ADD CONSTRAINT FK_9DD8F0E88AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE poste DROP couleur');
    }
}
