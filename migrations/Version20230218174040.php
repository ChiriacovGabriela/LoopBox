<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218174040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE song_user (song_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FE0000BCA0BDB2F3 (song_id), INDEX IDX_FE0000BCA76ED395 (user_id), PRIMARY KEY(song_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_user ADD CONSTRAINT FK_FE0000BCA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_user ADD CONSTRAINT FK_FE0000BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_user DROP FOREIGN KEY FK_FE0000BCA0BDB2F3');
        $this->addSql('ALTER TABLE song_user DROP FOREIGN KEY FK_FE0000BCA76ED395');
        $this->addSql('DROP TABLE song_user');
    }
}
