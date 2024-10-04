<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220616192310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE offrespays');
        $this->addSql('ALTER TABLE croisiere_excursion ADD pays_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE croisiere_excursion ADD CONSTRAINT FK_75A96F17A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('CREATE INDEX IDX_75A96F17A6E44244 ON croisiere_excursion (pays_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offrespays (offres_id INT NOT NULL, pays_id INT NOT NULL, INDEX IDX_72FC40DF6C83CD9F (offres_id), INDEX IDX_72FC40DFA6E44244 (pays_id), PRIMARY KEY(offres_id, pays_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE offrespays ADD CONSTRAINT FK_72FC40DFA6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE offrespays ADD CONSTRAINT FK_72FC40DF6C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id)');
        $this->addSql('ALTER TABLE croisiere_excursion DROP FOREIGN KEY FK_75A96F17A6E44244');
        $this->addSql('DROP INDEX IDX_75A96F17A6E44244 ON croisiere_excursion');
        $this->addSql('ALTER TABLE croisiere_excursion DROP pays_id');
    }
}
