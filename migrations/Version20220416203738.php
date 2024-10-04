<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220416203738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pays_offres');
        $this->addSql('ALTER TABLE reservation ADD grille_tarifaire_id INT DEFAULT NULL, ADD agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552C47CC22 FOREIGN KEY (grille_tarifaire_id) REFERENCES grille_tarifaire (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_42C849552C47CC22 ON reservation (grille_tarifaire_id)');
        $this->addSql('CREATE INDEX IDX_42C84955D725330D ON reservation (agence_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pays_offres (pays_id INT NOT NULL, offres_id INT NOT NULL, INDEX IDX_77AC62156C83CD9F (offres_id), INDEX IDX_77AC6215A6E44244 (pays_id), PRIMARY KEY(pays_id, offres_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pays_offres ADD CONSTRAINT FK_77AC6215A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pays_offres ADD CONSTRAINT FK_77AC62156C83CD9F FOREIGN KEY (offres_id) REFERENCES offres (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552C47CC22');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955D725330D');
        $this->addSql('DROP INDEX IDX_42C849552C47CC22 ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955D725330D ON reservation');
        $this->addSql('ALTER TABLE reservation DROP grille_tarifaire_id, DROP agence_id');
    }
}
