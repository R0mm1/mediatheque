<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180901145521 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, electronic_book_id INT DEFAULT NULL, paper_book_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, year VARCHAR(4) DEFAULT NULL, page_count INT DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_CBE5A331AC578236 (electronic_book_id), UNIQUE INDEX UNIQ_CBE5A3311606E160 (paper_book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paper_book (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) NOT NULL, bearth_year VARCHAR(4) DEFAULT NULL, death_year VARCHAR(4) DEFAULT NULL, biography LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE electronic_book (id INT AUTO_INCREMENT NOT NULL, file VARCHAR(255) NOT NULL, mime_type VARCHAR(50) DEFAULT NULL, size NUMERIC(6, 2) DEFAULT NULL, language VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331AC578236 FOREIGN KEY (electronic_book_id) REFERENCES electronic_book (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3311606E160 FOREIGN KEY (paper_book_id) REFERENCES paper_book (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3311606E160');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331AC578236');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE paper_book');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE electronic_book');
        $this->addSql('DROP TABLE editor');
    }
}
