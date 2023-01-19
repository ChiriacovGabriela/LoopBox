<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230119184802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE song_playlist (song_id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_7C5E4765A0BDB2F3 (song_id), INDEX IDX_7C5E47656BBD148 (playlist_id), PRIMARY KEY(song_id, playlist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_album (song_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_F43CFB06A0BDB2F3 (song_id), INDEX IDX_F43CFB061137ABCF (album_id), PRIMARY KEY(song_id, album_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_playlist ADD CONSTRAINT FK_7C5E4765A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_playlist ADD CONSTRAINT FK_7C5E47656BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_album ADD CONSTRAINT FK_F43CFB06A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_album ADD CONSTRAINT FK_F43CFB061137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE album ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_39986E43A76ED395 ON album (user_id)');
        $this->addSql('ALTER TABLE comment ADD song_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('CREATE INDEX IDX_9474526CA0BDB2F3 ON comment (song_id)');
        $this->addSql('ALTER TABLE playlist ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE playlist ADD CONSTRAINT FK_D782112DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D782112DA76ED395 ON playlist (user_id)');
        $this->addSql('ALTER TABLE song ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_33EDEEA1A76ED395 ON song (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_playlist DROP FOREIGN KEY FK_7C5E4765A0BDB2F3');
        $this->addSql('ALTER TABLE song_playlist DROP FOREIGN KEY FK_7C5E47656BBD148');
        $this->addSql('ALTER TABLE song_album DROP FOREIGN KEY FK_F43CFB06A0BDB2F3');
        $this->addSql('ALTER TABLE song_album DROP FOREIGN KEY FK_F43CFB061137ABCF');
        $this->addSql('DROP TABLE song_playlist');
        $this->addSql('DROP TABLE song_album');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43A76ED395');
        $this->addSql('DROP INDEX IDX_39986E43A76ED395 ON album');
        $this->addSql('ALTER TABLE album DROP user_id');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA0BDB2F3');
        $this->addSql('DROP INDEX IDX_9474526CA0BDB2F3 ON comment');
        $this->addSql('ALTER TABLE comment DROP song_id');
        $this->addSql('ALTER TABLE playlist DROP FOREIGN KEY FK_D782112DA76ED395');
        $this->addSql('DROP INDEX IDX_D782112DA76ED395 ON playlist');
        $this->addSql('ALTER TABLE playlist DROP user_id');
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA1A76ED395');
        $this->addSql('DROP INDEX IDX_33EDEEA1A76ED395 ON song');
        $this->addSql('ALTER TABLE song DROP user_id');
    }
}
