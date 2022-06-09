<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220604121303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audio_book (id INT NOT NULL, book_file_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8454BF6DEB51FA (book_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audio_book_file (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audio_book ADD CONSTRAINT FK_8454BF6DEB51FA FOREIGN KEY (book_file_id) REFERENCES audio_book_file (id)');
        $this->addSql('ALTER TABLE audio_book ADD CONSTRAINT FK_8454BF6DBF396750 FOREIGN KEY (id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE audio_book_file ADD CONSTRAINT FK_C1537018BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audio_book DROP FOREIGN KEY FK_8454BF6DEB51FA');
        $this->addSql('DROP TABLE audio_book');
        $this->addSql('DROP TABLE audio_book_file');
    }
}
