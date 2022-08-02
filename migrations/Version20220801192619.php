<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220801192619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, autor VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, is_published TINYINT(1) NOT NULL, rating SMALLINT DEFAULT NULL, INDEX IDX_5F9E962A4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE productcategory ADD slug VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC58E0A285');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC8FABDD9F');
        $this->addSql('DROP INDEX IDX_67F068BC8FABDD9F ON commentaire');
        $this->addSql('DROP INDEX IDX_67F068BC58E0A285 ON commentaire');
        $this->addSql('ALTER TABLE commentaire ADD user_id INT DEFAULT NULL, ADD blog_id INT DEFAULT NULL, DROP userid_id, DROP blog_id_id, DROP name, DROP email');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES `User` (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCDAE07E97 FOREIGN KEY (blog_id) REFERENCES posts (id)');
        $this->addSql('CREATE INDEX IDX_67F068BCA76ED395 ON commentaire (user_id)');
        $this->addSql('CREATE INDEX IDX_67F068BCDAE07E97 ON commentaire (blog_id)');
        $this->addSql('ALTER TABLE post_category ADD category_description LONGTEXT DEFAULT NULL, DROP post_category_image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9A190605E237E06 ON post_category (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFA2B36786B ON posts (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comments');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCDAE07E97');
        $this->addSql('DROP INDEX IDX_67F068BCA76ED395 ON commentaire');
        $this->addSql('DROP INDEX IDX_67F068BCDAE07E97 ON commentaire');
        $this->addSql('ALTER TABLE commentaire ADD userid_id INT DEFAULT NULL, ADD blog_id_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, DROP user_id, DROP blog_id');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC8FABDD9F FOREIGN KEY (blog_id_id) REFERENCES posts (id)');
        $this->addSql('CREATE INDEX IDX_67F068BC8FABDD9F ON commentaire (blog_id_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC58E0A285 ON commentaire (userid_id)');
        $this->addSql('DROP INDEX UNIQ_B9A190605E237E06 ON post_category');
        $this->addSql('ALTER TABLE post_category ADD post_category_image VARCHAR(255) NOT NULL, DROP category_description');
        $this->addSql('DROP INDEX UNIQ_885DBAFA2B36786B ON posts');
        $this->addSql('ALTER TABLE `ProductCategory` DROP slug');
    }
}
