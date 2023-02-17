<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215204112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE song_album (song_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_F43CFB06A0BDB2F3 (song_id), INDEX IDX_F43CFB061137ABCF (album_id), PRIMARY KEY(song_id, album_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_album ADD CONSTRAINT FK_F43CFB06A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_album ADD CONSTRAINT FK_F43CFB061137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE album ADD type VARCHAR(100) DEFAULT NULL, ADD artist VARCHAR(100) DEFAULT NULL, ADD picture_file_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_album DROP FOREIGN KEY FK_F43CFB06A0BDB2F3');
        $this->addSql('ALTER TABLE song_album DROP FOREIGN KEY FK_F43CFB061137ABCF');
        $this->addSql('DROP TABLE song_album');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43A76ED395');
        $this->addSql('ALTER TABLE album DROP type, DROP artist, DROP picture_file_name');
    }
}
