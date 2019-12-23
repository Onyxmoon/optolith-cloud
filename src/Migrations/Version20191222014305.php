<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191222014305 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE character (id CHAR(36) NOT NULL --(DC2Type:guid)
        , display_name VARCHAR(255) NOT NULL, client_version VARCHAR(255) NOT NULL, data CLOB NOT NULL --(DC2Type:json)
        , last_modification_date DATETIME NOT NULL, checksum CLOB NOT NULL, owner_id CHAR(36) NOT NULL --(DC2Type:guid)
        , avatar_id INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE media_object (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, file_path VARCHAR(255) DEFAULT NULL, owner_id CHAR(36) NOT NULL --(DC2Type:guid)
        )');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL --(DC2Type:guid)
        , email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, display_name CLOB NOT NULL, is_active BOOLEAN NOT NULL, registration_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , confirmation_secret CHAR(36) NOT NULL --(DC2Type:guid)
        , confirmation_type VARCHAR(50) DEFAULT NULL, confirmed_email BOOLEAN NOT NULL, new_email VARCHAR(255) DEFAULT NULL, locale VARCHAR(42) NOT NULL, avatar_id INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE character');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP TABLE user');
    }
}
