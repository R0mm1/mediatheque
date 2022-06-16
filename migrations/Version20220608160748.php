<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220608160748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audio_book_file_download_token (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', file_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_B2F15F4593CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE electronic_book_file_download_token (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', file_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_93388D9E93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_download_token (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, created DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_ED9294DCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audio_book_file_download_token ADD CONSTRAINT FK_B2F15F4593CB796C FOREIGN KEY (file_id) REFERENCES audio_book_file (id)');
        $this->addSql('ALTER TABLE audio_book_file_download_token ADD CONSTRAINT FK_B2F15F45BF396750 FOREIGN KEY (id) REFERENCES file_download_token (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE electronic_book_file_download_token ADD CONSTRAINT FK_93388D9E93CB796C FOREIGN KEY (file_id) REFERENCES book_file (id)');
        $this->addSql('ALTER TABLE electronic_book_file_download_token ADD CONSTRAINT FK_93388D9EBF396750 FOREIGN KEY (id) REFERENCES file_download_token (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_download_token ADD CONSTRAINT FK_ED9294DCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audio_book_file_download_token DROP FOREIGN KEY FK_B2F15F45BF396750');
        $this->addSql('ALTER TABLE electronic_book_file_download_token DROP FOREIGN KEY FK_93388D9EBF396750');
        $this->addSql('DROP TABLE audio_book_file_download_token');
        $this->addSql('DROP TABLE electronic_book_file_download_token');
        $this->addSql('DROP TABLE file_download_token');
    }
}
