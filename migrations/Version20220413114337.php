<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413114337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE croisiere_excursion (id INT AUTO_INCREMENT NOT NULL, croisiere_id INT DEFAULT NULL, excursion_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_75A96F17205952B7 (croisiere_id), INDEX IDX_75A96F174AB4296F (excursion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE croisiere_excursion ADD CONSTRAINT FK_75A96F17205952B7 FOREIGN KEY (croisiere_id) REFERENCES croisiere (id)');
        $this->addSql('ALTER TABLE croisiere_excursion ADD CONSTRAINT FK_75A96F174AB4296F FOREIGN KEY (excursion_id) REFERENCES excursion (id)');
        $this->addSql('ALTER TABLE images ADD croisiere_excursion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A32471C94 FOREIGN KEY (croisiere_excursion_id) REFERENCES croisiere_excursion (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A32471C94 ON images (croisiere_excursion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A32471C94');
        $this->addSql('DROP TABLE croisiere_excursion');
        $this->addSql('DROP INDEX IDX_E01FBE6A32471C94 ON images');
        $this->addSql('ALTER TABLE images DROP croisiere_excursion_id');
    }
}
