<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220124112905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) NOT NULL, bearth_year VARCHAR(4) DEFAULT NULL, death_year VARCHAR(4) DEFAULT NULL, biography LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, cover_id INT DEFAULT NULL, owner_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, year VARCHAR(4) DEFAULT NULL, page_count INT DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, language VARCHAR(50) DEFAULT NULL, summary LONGTEXT DEFAULT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_CBE5A331922726E9 (cover_id), INDEX IDX_CBE5A3317E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE books_authors (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_877EACC216A2B381 (book_id), INDEX IDX_877EACC2F675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_file (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_notation (id INT NOT NULL, book_id INT DEFAULT NULL, INDEX IDX_1B97AACC16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cover (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE electronic_book (id INT NOT NULL, book_file_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7602E9E3EB51FA (book_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) DEFAULT NULL, status INT NOT NULL, updated_at DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notation (id INT AUTO_INCREMENT NOT NULL, user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', note DOUBLE PRECISION NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_268BC95A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paper_book (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reference_group (id INT AUTO_INCREMENT NOT NULL, comment LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_group (book_id INT NOT NULL, referenceGroup_id INT NOT NULL, INDEX IDX_630251BC741AFFC4 (referenceGroup_id), INDEX IDX_630251BC16A2B381 (book_id), PRIMARY KEY(referenceGroup_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', sub VARCHAR(255) NOT NULL, last_jit VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331922726E9 FOREIGN KEY (cover_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3317E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE books_authors ADD CONSTRAINT FK_877EACC216A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE books_authors ADD CONSTRAINT FK_877EACC2F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE book_file ADD CONSTRAINT FK_95027D18BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_notation ADD CONSTRAINT FK_1B97AACC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_notation ADD CONSTRAINT FK_1B97AACCBF396750 FOREIGN KEY (id) REFERENCES notation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cover ADD CONSTRAINT FK_8D0886C5BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE electronic_book ADD CONSTRAINT FK_7602E9E3EB51FA FOREIGN KEY (book_file_id) REFERENCES book_file (id)');
        $this->addSql('ALTER TABLE electronic_book ADD CONSTRAINT FK_7602E9E3BF396750 FOREIGN KEY (id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE paper_book ADD CONSTRAINT FK_3D0D0226BF396750 FOREIGN KEY (id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_group ADD CONSTRAINT FK_630251BC741AFFC4 FOREIGN KEY (referenceGroup_id) REFERENCES reference_group (id)');
        $this->addSql('ALTER TABLE book_group ADD CONSTRAINT FK_630251BC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books_authors DROP FOREIGN KEY FK_877EACC2F675F31B');
        $this->addSql('ALTER TABLE books_authors DROP FOREIGN KEY FK_877EACC216A2B381');
        $this->addSql('ALTER TABLE book_notation DROP FOREIGN KEY FK_1B97AACC16A2B381');
        $this->addSql('ALTER TABLE electronic_book DROP FOREIGN KEY FK_7602E9E3BF396750');
        $this->addSql('ALTER TABLE paper_book DROP FOREIGN KEY FK_3D0D0226BF396750');
        $this->addSql('ALTER TABLE book_group DROP FOREIGN KEY FK_630251BC16A2B381');
        $this->addSql('ALTER TABLE electronic_book DROP FOREIGN KEY FK_7602E9E3EB51FA');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331922726E9');
        $this->addSql('ALTER TABLE book_file DROP FOREIGN KEY FK_95027D18BF396750');
        $this->addSql('ALTER TABLE cover DROP FOREIGN KEY FK_8D0886C5BF396750');
        $this->addSql('ALTER TABLE book_notation DROP FOREIGN KEY FK_1B97AACCBF396750');
        $this->addSql('ALTER TABLE book_group DROP FOREIGN KEY FK_630251BC741AFFC4');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3317E3C61F9');
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC95A76ED395');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE books_authors');
        $this->addSql('DROP TABLE book_file');
        $this->addSql('DROP TABLE book_notation');
        $this->addSql('DROP TABLE cover');
        $this->addSql('DROP TABLE editor');
        $this->addSql('DROP TABLE electronic_book');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE notation');
        $this->addSql('DROP TABLE paper_book');
        $this->addSql('DROP TABLE reference_group');
        $this->addSql('DROP TABLE book_group');
        $this->addSql('DROP TABLE user');
    }
}
