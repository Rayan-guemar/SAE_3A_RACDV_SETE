<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231006014132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE disponibilites DROP FOREIGN KEY disponibilites_ibfk_1');
        $this->addSql('ALTER TABLE disponibilites DROP FOREIGN KEY disponibilites_ibfk_2');
        $this->addSql('ALTER TABLE preferences DROP FOREIGN KEY preferences_ibfk_1');
        $this->addSql('ALTER TABLE preferences DROP FOREIGN KEY preferences_ibfk_2');
        $this->addSql('ALTER TABLE preferences DROP FOREIGN KEY preferences_ibfk_3');
        $this->addSql('ALTER TABLE affecte DROP FOREIGN KEY affecte_ibfk_1');
        $this->addSql('ALTER TABLE affecte DROP FOREIGN KEY affecte_ibfk_2');
        $this->addSql('ALTER TABLE est_responsable DROP FOREIGN KEY est_responsable_ibfk_2');
        $this->addSql('ALTER TABLE est_responsable DROP FOREIGN KEY est_responsable_ibfk_1');
        $this->addSql('ALTER TABLE est_benevole DROP FOREIGN KEY est_benevole_ibfk_1');
        $this->addSql('ALTER TABLE est_benevole DROP FOREIGN KEY est_benevole_ibfk_2');
        $this->addSql('DROP TABLE disponibilites');
        $this->addSql('DROP TABLE preferences');
        $this->addSql('DROP TABLE affecte');
        $this->addSql('DROP TABLE est_responsable');
        $this->addSql('DROP TABLE est_benevole');
        $this->addSql('ALTER TABLE demande_festival DROP affiche_festival');
        $this->addSql('ALTER TABLE festival DROP affiche');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE disponibilites (id_utilisateur INT NOT NULL, id_crenaux INT NOT NULL, INDEX id_crenaux (id_crenaux), INDEX IDX_B0F3489C50EAE44 (id_utilisateur), PRIMARY KEY(id_utilisateur, id_crenaux)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE preferences (id_utilisateur INT NOT NULL, id_tache INT NOT NULL, id_festival INT NOT NULL, niveau_pref INT DEFAULT NULL, INDEX id_festival (id_festival), INDEX id_tache (id_tache), INDEX IDX_E931A6F550EAE44 (id_utilisateur), PRIMARY KEY(id_utilisateur, id_tache, id_festival)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE affecte (id_utilisateur INT NOT NULL, id_crenaux INT NOT NULL, INDEX id_crenaux (id_crenaux), INDEX IDX_4890ADE850EAE44 (id_utilisateur), PRIMARY KEY(id_utilisateur, id_crenaux)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE est_responsable (id_utilisateur INT NOT NULL, id_festival INT NOT NULL, INDEX id_festival (id_festival), INDEX IDX_3D13019550EAE44 (id_utilisateur), PRIMARY KEY(id_utilisateur, id_festival)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE est_benevole (id_utilisateur INT NOT NULL, id_festival INT NOT NULL, INDEX id_festival (id_festival), INDEX IDX_2F8ED27250EAE44 (id_utilisateur), PRIMARY KEY(id_utilisateur, id_festival)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE disponibilites ADD CONSTRAINT disponibilites_ibfk_1 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE disponibilites ADD CONSTRAINT disponibilites_ibfk_2 FOREIGN KEY (id_crenaux) REFERENCES creneaux (id)');
        $this->addSql('ALTER TABLE preferences ADD CONSTRAINT preferences_ibfk_1 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE preferences ADD CONSTRAINT preferences_ibfk_2 FOREIGN KEY (id_tache) REFERENCES tache (id)');
        $this->addSql('ALTER TABLE preferences ADD CONSTRAINT preferences_ibfk_3 FOREIGN KEY (id_festival) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE affecte ADD CONSTRAINT affecte_ibfk_1 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE affecte ADD CONSTRAINT affecte_ibfk_2 FOREIGN KEY (id_crenaux) REFERENCES creneaux (id)');
        $this->addSql('ALTER TABLE est_responsable ADD CONSTRAINT est_responsable_ibfk_2 FOREIGN KEY (id_festival) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE est_responsable ADD CONSTRAINT est_responsable_ibfk_1 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE est_benevole ADD CONSTRAINT est_benevole_ibfk_1 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE est_benevole ADD CONSTRAINT est_benevole_ibfk_2 FOREIGN KEY (id_festival) REFERENCES festival (id)');
        $this->addSql('ALTER TABLE festival ADD affiche VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE demande_festival ADD affiche_festival VARCHAR(255) NOT NULL');
    }
}
