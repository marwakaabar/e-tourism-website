<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407142021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offres (id INT AUTO_INCREMENT NOT NULL, agence_id INT DEFAULT NULL, hotel_id INT DEFAULT NULL, pays_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, total_rate DOUBLE PRECISION NOT NULL, prix DOUBLE PRECISION NOT NULL, inclus VARCHAR(255) NOT NULL, non_inclus VARCHAR(255) NOT NULL, INDEX IDX_C6AC3544D725330D (agence_id), INDEX IDX_C6AC35443243BB18 (hotel_id), INDEX IDX_C6AC3544A6E44244 (pays_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_C6AC3544D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_C6AC35443243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_C6AC3544A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE images ADD offres_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A6C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A6C83CD9F ON images (offres_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A6C83CD9F');
        $this->addSql('DROP TABLE offres');
        $this->addSql('DROP INDEX IDX_E01FBE6A6C83CD9F ON images');
        $this->addSql('ALTER TABLE images DROP offres_id');
    }
}
