<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220803175409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD blocked TINYINT(1) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9A190605E237E06 ON post_category (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFA2B36786B ON posts (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_B9A190605E237E06 ON post_category');
        $this->addSql('DROP INDEX UNIQ_885DBAFA2B36786B ON posts');
        $this->addSql('ALTER TABLE `User` DROP created_at, DROP updated_at, DROP blocked');
    }
}
