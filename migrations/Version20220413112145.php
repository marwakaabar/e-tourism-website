<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413112145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images ADD voyage_excursion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A725FFF33 FOREIGN KEY (voyage_excursion_id) REFERENCES voyage_excursion (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A725FFF33 ON images (voyage_excursion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A725FFF33');
        $this->addSql('DROP INDEX IDX_E01FBE6A725FFF33 ON images');
        $this->addSql('ALTER TABLE images DROP voyage_excursion_id');
    }
}
