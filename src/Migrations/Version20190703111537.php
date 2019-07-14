<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190703111537 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_tokens DROP FOREIGN KEY FK_58D184BCA76ED395');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE refresh_tokens DROP FOREIGN KEY FK_9BACE7E1A76ED395');

        //Create new tables
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) DEFAULT NULL, status INT NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cover (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reference_group (id INT AUTO_INCREMENT NOT NULL, comment LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_group (book_id INT NOT NULL, referenceGroup_id INT NOT NULL, INDEX IDX_630251BC741AFFC4 (referenceGroup_id), INDEX IDX_630251BC16A2B381 (book_id), PRIMARY KEY(referenceGroup_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cover ADD CONSTRAINT FK_8D0886C5BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_group ADD CONSTRAINT FK_630251BC741AFFC4 FOREIGN KEY (referenceGroup_id) REFERENCES reference_group (id)');
        $this->addSql('ALTER TABLE book_group ADD CONSTRAINT FK_630251BC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');

        //Users migration
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('INSERT INTO user SELECT id, username, "[]", "" FROM fos_user');

        //Electronic book
        $this->addSql('CREATE TEMPORARY TABLE electronic_book_bckp SELECT * FROM electronic_book');
        $this->addSql('ALTER TABLE electronic_book DROP file, DROP mime_type, DROP size');
        $this->addSql('INSERT INTO file SELECT id, file, "2", "electronicbook" FROM electronic_book_bckp');
        $this->addSql('ALTER TABLE electronic_book ADD CONSTRAINT FK_7602E9E3BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE');

        //Covers
        $this->addSql('ALTER TABLE book ADD cover_id INT DEFAULT NULL');
        $this->addSql('INSERT INTO file (`path`, `status`, `discr`) SELECT picture, "2", "cover" FROM book;');
        $this->addSql('INSERT INTO cover SELECT id FROM file WHERE discr="cover"');
        $this->addSql('UPDATE book SET cover_id = (SELECT id FROM file WHERE path=book.picture);');
        $this->addSql('ALTER TABLE book DROP picture');

        //Others book modifications
        $this->addSql('ALTER TABLE book ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331922726E9 FOREIGN KEY (cover_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3317E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');

        //Authentication
        $this->addSql('ALTER TABLE access_tokens ADD CONSTRAINT FK_58D184BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE refresh_tokens ADD CONSTRAINT FK_9BACE7E1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');

        //Add indexes
        $this->addSql('CREATE INDEX IDX_CBE5A331922726E9 ON book (cover_id)');
        $this->addSql('CREATE INDEX IDX_CBE5A3317E3C61F9 ON book (owner_id)');

        //Drop old tables
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE electronic_book_bckp');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_tokens DROP FOREIGN KEY FK_58D184BCA76ED395');
        $this->addSql('ALTER TABLE refresh_tokens DROP FOREIGN KEY FK_9BACE7E1A76ED395');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3317E3C61F9');
        $this->addSql('ALTER TABLE electronic_book DROP FOREIGN KEY FK_7602E9E3BF396750');
        $this->addSql('ALTER TABLE cover DROP FOREIGN KEY FK_8D0886C5BF396750');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331922726E9');
        $this->addSql('ALTER TABLE book_group DROP FOREIGN KEY FK_630251BC741AFFC4');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, username_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, email VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, email_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, password VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL COLLATE utf8mb4_unicode_ci, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE cover');
        $this->addSql('DROP TABLE reference_group');
        $this->addSql('DROP TABLE book_group');
        $this->addSql('ALTER TABLE access_tokens DROP FOREIGN KEY FK_58D184BCA76ED395');
        $this->addSql('ALTER TABLE access_tokens ADD CONSTRAINT FK_58D184BCA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_CBE5A331922726E9 ON book');
        $this->addSql('DROP INDEX IDX_CBE5A3317E3C61F9 ON book');
        $this->addSql('ALTER TABLE book ADD picture LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP cover_id, DROP owner_id');
        $this->addSql('ALTER TABLE electronic_book ADD file VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD mime_type VARCHAR(50) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD size INT DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE refresh_tokens DROP FOREIGN KEY FK_9BACE7E1A76ED395');
        $this->addSql('ALTER TABLE refresh_tokens ADD CONSTRAINT FK_9BACE7E1A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
    }
}
