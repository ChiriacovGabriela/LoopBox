<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230415194820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album CHANGE name name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment DROP deleted_at');
        $this->addSql('ALTER TABLE playlist DROP deleted_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album CHANGE name name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE playlist ADD deleted_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD deleted_at DATE DEFAULT NULL');
    }
}
