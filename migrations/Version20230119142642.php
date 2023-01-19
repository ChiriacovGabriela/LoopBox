<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230119142642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATE NOT NULL, deleted_at DATE DEFAULT NULL, updated_at DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE palylist (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, image_path VARCHAR(255) DEFAULT NULL, visibility TINYINT(1) NOT NULL, creted_at DATE NOT NULL, deleted_at DATE DEFAULT NULL, updated_at DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD created_at DATE NOT NULL, ADD deleted_at DATE DEFAULT NULL, ADD updated_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE song ADD type VARCHAR(100) DEFAULT NULL, ADD artist VARCHAR(100) DEFAULT NULL, ADD created_at DATE NOT NULL, ADD deleted_at DATE DEFAULT NULL, ADD updated_at DATE DEFAULT NULL, ADD picture_path VARCHAR(255) DEFAULT NULL, ADD audio_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(100) NOT NULL, ADD surname VARCHAR(100) NOT NULL, ADD preference VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE palylist');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('ALTER TABLE comment DROP created_at, DROP deleted_at, DROP updated_at');
        $this->addSql('ALTER TABLE song DROP type, DROP artist, DROP created_at, DROP deleted_at, DROP updated_at, DROP picture_path, DROP audio_path');
        $this->addSql('ALTER TABLE user DROP name, DROP surname, DROP preference');
    }
}
