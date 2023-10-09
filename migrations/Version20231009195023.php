<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009195023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE festival_utilisateur (festival_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_B8D5D68A8AEBAF57 (festival_id), INDEX IDX_B8D5D68AFB88E14F (utilisateur_id), PRIMARY KEY(festival_id, utilisateur_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE festival_utilisateur ADD CONSTRAINT FK_B8D5D68A8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE festival_utilisateur ADD CONSTRAINT FK_B8D5D68AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE festival_utilisateur DROP FOREIGN KEY FK_B8D5D68A8AEBAF57');
        $this->addSql('ALTER TABLE festival_utilisateur DROP FOREIGN KEY FK_B8D5D68AFB88E14F');
        $this->addSql('DROP TABLE festival_utilisateur');
    }
}
