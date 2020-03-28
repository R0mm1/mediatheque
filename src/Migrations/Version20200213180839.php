<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200213180839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_tokens DROP FOREIGN KEY FK_58D184BC19EB6921');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02C19EB6921');
        $this->addSql('DROP TABLE access_tokens');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE access_tokens (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, token VARCHAR(128) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_58D184BC19EB6921 (client_id), INDEX IDX_58D184BCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, redirect_uri LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, token VARCHAR(128) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_5933D02C19EB6921 (client_id), INDEX IDX_5933D02CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, redirect_uris LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', secret VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, allowed_grant_types LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE access_tokens ADD CONSTRAINT FK_58D184BC19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE access_tokens ADD CONSTRAINT FK_58D184BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens');
        $this->addSql('ALTER TABLE refresh_tokens ADD client_id INT NOT NULL, ADD user_id INT DEFAULT NULL, ADD expires_at INT DEFAULT NULL, ADD scope VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP username, DROP valid, CHANGE refresh_token token VARCHAR(128) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE refresh_tokens ADD CONSTRAINT FK_9BACE7E119EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE refresh_tokens ADD CONSTRAINT FK_9BACE7E1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9BACE7E1A76ED395 ON refresh_tokens (user_id)');
        $this->addSql('CREATE INDEX IDX_9BACE7E119EB6921 ON refresh_tokens (client_id)');
    }
}
