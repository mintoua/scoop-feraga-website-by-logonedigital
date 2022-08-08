<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220808112213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9A190605E237E06 ON post_category (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFA2B36786B ON posts (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_B9A190605E237E06 ON post_category');
        $this->addSql('DROP INDEX UNIQ_885DBAFA2B36786B ON posts');
    }
}
