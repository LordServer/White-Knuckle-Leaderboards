<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512221351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_subcategory DROP CONSTRAINT fk_ba47e62312469de2');
        $this->addSql('ALTER TABLE category_subcategory DROP CONSTRAINT fk_ba47e6235dc6fe57');
        $this->addSql('DROP TABLE category_subcategory');
        $this->addSql('ALTER TABLE category ADD subcategory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C15DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id)');
        $this->addSql('CREATE INDEX IDX_64C19C15DC6FE57 ON category (subcategory_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_subcategory (category_id INT NOT NULL, subcategory_id INT NOT NULL, PRIMARY KEY (category_id, subcategory_id))');
        $this->addSql('CREATE INDEX idx_ba47e6235dc6fe57 ON category_subcategory (subcategory_id)');
        $this->addSql('CREATE INDEX idx_ba47e62312469de2 ON category_subcategory (category_id)');
        $this->addSql('ALTER TABLE category_subcategory ADD CONSTRAINT fk_ba47e62312469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_subcategory ADD CONSTRAINT fk_ba47e6235dc6fe57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C15DC6FE57');
        $this->addSql('DROP INDEX IDX_64C19C15DC6FE57');
        $this->addSql('ALTER TABLE category DROP subcategory_id');
    }
}
