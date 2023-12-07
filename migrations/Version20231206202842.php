<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231206202842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed DATETIME NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usuario ADD last_login_at DATETIME DEFAULT NULL, ADD last_login_ip VARCHAR(255) DEFAULT NULL, CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE is_verified is_verified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE usuario RENAME INDEX email_unique TO UNIQ_2265B05DE7927C74');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX queue_name_index TO IDX_75EA56E0FB7336F0');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX available_at_index TO IDX_75EA56E0E3BD61CE');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX delivered_at_index TO IDX_75EA56E016BA31DB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rememberme_token');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_75ea56e0e3bd61ce TO available_at_INDEX');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_75ea56e016ba31db TO delivered_at_INDEX');
        $this->addSql('ALTER TABLE messenger_messages RENAME INDEX idx_75ea56e0fb7336f0 TO queue_name_INDEX');
        $this->addSql('ALTER TABLE usuario DROP last_login_at, DROP last_login_ip, CHANGE id id BINARY(16) NOT NULL, CHANGE is_verified is_verified TINYINT(1) DEFAULT 0 NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE usuario RENAME INDEX uniq_2265b05de7927c74 TO email_UNIQUE');
    }
}
