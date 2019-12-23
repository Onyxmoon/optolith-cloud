<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191222032540 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__media_object AS SELECT id, file_path, owner_id FROM media_object');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('CREATE TABLE media_object (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , file_path VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO media_object (id, file_path, owner_id) SELECT id, file_path, owner_id FROM __temp__media_object');
        $this->addSql('DROP TABLE __temp__media_object');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password, display_name, is_active, registration_date, confirmation_secret, confirmation_type, confirmed_email, new_email, locale, avatar_id FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , email VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY, display_name CLOB NOT NULL COLLATE BINARY, is_active BOOLEAN NOT NULL, registration_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , confirmation_type VARCHAR(50) DEFAULT NULL COLLATE BINARY, confirmed_email BOOLEAN NOT NULL, new_email VARCHAR(255) DEFAULT NULL COLLATE BINARY, locale VARCHAR(42) NOT NULL COLLATE BINARY, avatar_id INTEGER DEFAULT NULL, confirmation_secret CHAR(36) DEFAULT NULL --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, email, roles, password, display_name, is_active, registration_date, confirmation_secret, confirmation_type, confirmed_email, new_email, locale, avatar_id) SELECT id, email, roles, password, display_name, is_active, registration_date, confirmation_secret, confirmation_type, confirmed_email, new_email, locale, avatar_id FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__media_object AS SELECT id, file_path, owner_id FROM media_object');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('CREATE TABLE media_object (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id CHAR(36) NOT NULL --(DC2Type:guid)
        , file_path CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:guid)
        )');
        $this->addSql('INSERT INTO media_object (id, file_path, owner_id) SELECT id, file_path, owner_id FROM __temp__media_object');
        $this->addSql('DROP TABLE __temp__media_object');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password, display_name, is_active, registration_date, confirmation_secret, confirmation_type, confirmed_email, new_email, locale, avatar_id FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL --(DC2Type:guid)
        , email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, display_name CLOB NOT NULL, is_active BOOLEAN NOT NULL, registration_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , confirmation_type VARCHAR(50) DEFAULT NULL, confirmed_email BOOLEAN NOT NULL, new_email VARCHAR(255) DEFAULT NULL, locale VARCHAR(42) NOT NULL, avatar_id INTEGER DEFAULT NULL, confirmation_secret CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, email, roles, password, display_name, is_active, registration_date, confirmation_secret, confirmation_type, confirmed_email, new_email, locale, avatar_id) SELECT id, email, roles, password, display_name, is_active, registration_date, confirmation_secret, confirmation_type, confirmed_email, new_email, locale, avatar_id FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }
}
