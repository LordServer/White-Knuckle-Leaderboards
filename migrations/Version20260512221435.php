<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512221435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_subcategory (category_id INT NOT NULL, subcategory_id INT NOT NULL, PRIMARY KEY (category_id, subcategory_id))');
        $this->addSql('CREATE INDEX IDX_BA47E62312469DE2 ON category_subcategory (category_id)');
        $this->addSql('CREATE INDEX IDX_BA47E6235DC6FE57 ON category_subcategory (subcategory_id)');
        $this->addSql('ALTER TABLE category_subcategory ADD CONSTRAINT FK_BA47E62312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_subcategory ADD CONSTRAINT FK_BA47E6235DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_rank_method DROP CONSTRAINT fk_675193bd12469de2');
        $this->addSql('ALTER TABLE category_rank_method DROP CONSTRAINT fk_675193bdcd75392');
        $this->addSql('DROP TABLE category_rank_method');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT fk_64c19c15dc6fe57');
        $this->addSql('DROP INDEX idx_64c19c15dc6fe57');
        $this->addSql('ALTER TABLE category RENAME COLUMN subcategory_id TO rank_method_id');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1CD75392 FOREIGN KEY (rank_method_id) REFERENCES rank_method (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1CD75392 ON category (rank_method_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_rank_method (category_id INT NOT NULL, rank_method_id INT NOT NULL, PRIMARY KEY (category_id, rank_method_id))');
        $this->addSql('CREATE INDEX idx_675193bdcd75392 ON category_rank_method (rank_method_id)');
        $this->addSql('CREATE INDEX idx_675193bd12469de2 ON category_rank_method (category_id)');
        $this->addSql('ALTER TABLE category_rank_method ADD CONSTRAINT fk_675193bd12469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_rank_method ADD CONSTRAINT fk_675193bdcd75392 FOREIGN KEY (rank_method_id) REFERENCES rank_method (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_subcategory DROP CONSTRAINT FK_BA47E62312469DE2');
        $this->addSql('ALTER TABLE category_subcategory DROP CONSTRAINT FK_BA47E6235DC6FE57');
        $this->addSql('DROP TABLE category_subcategory');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1CD75392');
        $this->addSql('DROP INDEX IDX_64C19C1CD75392');
        $this->addSql('ALTER TABLE category RENAME COLUMN rank_method_id TO subcategory_id');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT fk_64c19c15dc6fe57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_64c19c15dc6fe57 ON category (subcategory_id)');
    }
}
