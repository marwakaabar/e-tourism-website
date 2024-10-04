<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413104355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE voyage_excursion (id INT AUTO_INCREMENT NOT NULL, offre_id INT DEFAULT NULL, voyage_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_A9F1C9B14CC8505A (offre_id), INDEX IDX_A9F1C9B168C9E5AF (voyage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE voyage_excursion ADD CONSTRAINT FK_A9F1C9B14CC8505A FOREIGN KEY (offre_id) REFERENCES offres (id)');
        $this->addSql('ALTER TABLE voyage_excursion ADD CONSTRAINT FK_A9F1C9B168C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage_organiser (id)');
        $this->addSql('ALTER TABLE excursion ADD voyage_excursion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE excursion ADD CONSTRAINT FK_9B08E72F725FFF33 FOREIGN KEY (voyage_excursion_id) REFERENCES voyage_excursion (id)');
        $this->addSql('CREATE INDEX IDX_9B08E72F725FFF33 ON excursion (voyage_excursion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE excursion DROP FOREIGN KEY FK_9B08E72F725FFF33');
        $this->addSql('DROP TABLE voyage_excursion');
        $this->addSql('DROP INDEX IDX_9B08E72F725FFF33 ON excursion');
        $this->addSql('ALTER TABLE excursion DROP voyage_excursion_id');
    }
}
