<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607075306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add table to save site settings';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_settings (id INT NOT NULL, text_direction VARCHAR(255) DEFAULT NULL, favicon_url VARCHAR(255) DEFAULT NULL, logo_url VARCHAR(255) DEFAULT NULL, footer_logo_url VARCHAR(255) DEFAULT NULL, copyright_text LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_520_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE site_settings');
    }
}
