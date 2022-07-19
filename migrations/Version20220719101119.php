<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719101119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE farm_pictures ADD category_picture_id INT NOT NULL');
        $this->addSql('ALTER TABLE farm_pictures ADD CONSTRAINT FK_D777799AC85BEDC1 FOREIGN KEY (category_picture_id) REFERENCES category_picture (id)');
        $this->addSql('CREATE INDEX IDX_D777799AC85BEDC1 ON farm_pictures (category_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE farm_pictures DROP FOREIGN KEY FK_D777799AC85BEDC1');
        $this->addSql('DROP INDEX IDX_D777799AC85BEDC1 ON farm_pictures');
        $this->addSql('ALTER TABLE farm_pictures DROP category_picture_id');
    }
}
