<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220409110558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grille_tarifaire ADD offre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE grille_tarifaire ADD CONSTRAINT FK_63E2418B4CC8505A FOREIGN KEY (offre_id) REFERENCES offres (id)');
        $this->addSql('CREATE INDEX IDX_63E2418B4CC8505A ON grille_tarifaire (offre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grille_tarifaire DROP FOREIGN KEY FK_63E2418B4CC8505A');
        $this->addSql('DROP INDEX IDX_63E2418B4CC8505A ON grille_tarifaire');
        $this->addSql('ALTER TABLE grille_tarifaire DROP offre_id');
    }
}
