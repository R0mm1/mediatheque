<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191102162259 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $procedure = <<<EOT
        CREATE PROCEDURE version20191102162259()
        BEGIN
            DECLARE _id INTEGER;
            DECLARE _electronic_book_id INTEGER;
            DECLARE _paper_book_id INTEGER;
            DECLARE _title VARCHAR(255);
            DECLARE _year VARCHAR(4);
            DECLARE _page_count INTEGER;
            DECLARE _isbn VARCHAR(255);
            DECLARE _language VARCHAR(50);
            DECLARE _summary LONGTEXT;
            DECLARE _cover_id INTEGER;
            DECLARE _owner_id INTEGER;
            
            DECLARE _discr VARCHAR(50);
            
            DECLARE finished INTEGER DEFAULT 0;
            DECLARE books_cursor CURSOR FOR SELECT * FROM book_bckp;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;
                    
            CREATE TABLE book_bckp SELECT * FROM book;
            CREATE TABLE electronic_book_bckp SELECT * FROM electronic_book;
            CREATE TABLE paper_book_bckp SELECT * FROM paper_book;
            CREATE TABLE books_authors_bckp SELECT * from books_authors;
            CREATE TABLE book_group_bckp SELECT * FROM book_group;
            CREATE TABLE book_notation_bckp SELECT * FROM book_notation;
            
            DELETE FROM books_authors;
            DELETE FROM book_notation;
            DELETE FROM book_group;
            DELETE FROM book;
            DELETE FROM electronic_book;
            DELETE FROM paper_book;
            
            CREATE TABLE book_file (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
            ALTER TABLE book_file ADD CONSTRAINT FK_95027D18BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE;
            
            INSERT INTO book_file (`id`) SELECT id FROM file WHERE discr = 'electronicbook';
            UPDATE file SET discr='bookfile' WHERE discr='electronicbook';
            
            ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3311606E160;
            ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331AC578236;
            DROP INDEX UNIQ_CBE5A3311606E160 ON book;
            DROP INDEX UNIQ_CBE5A331AC578236 ON book;
            ALTER TABLE book ADD discr VARCHAR(255) NOT NULL, DROP electronic_book_id, DROP paper_book_id;
            ALTER TABLE electronic_book DROP FOREIGN KEY FK_7602E9E3BF396750;
            ALTER TABLE electronic_book ADD book_file_id INT DEFAULT NULL, CHANGE id id INT NOT NULL;
            ALTER TABLE electronic_book ADD CONSTRAINT FK_7602E9E3EB51FA FOREIGN KEY (book_file_id) REFERENCES book_file (id);
            ALTER TABLE electronic_book ADD CONSTRAINT FK_7602E9E3BF396750 FOREIGN KEY (id) REFERENCES book (id) ON DELETE CASCADE;
            CREATE INDEX IDX_7602E9E3EB51FA ON electronic_book (book_file_id);
            ALTER TABLE paper_book CHANGE id id INT NOT NULL;
            ALTER TABLE paper_book ADD CONSTRAINT FK_3D0D0226BF396750 FOREIGN KEY (id) REFERENCES book (id) ON DELETE CASCADE;
                
            OPEN books_cursor;
            booksLoop: LOOP
                FETCH books_cursor INTO _id, _electronic_book_id, _paper_book_id, _title, _year, _page_count, _isbn, _language, _summary, _cover_id, _owner_id;
                IF finished = 1 THEN
                    LEAVE booksLoop;
                END IF;
                
                INSERT INTO book (`title`, `year`, `page_count`, `isbn`, `language`, `summary`, `cover_id`, `owner_id`, `discr`) 
                    VALUES(_title, _year, _page_count, _isbn, _language, _summary, _cover_id, _owner_id, _discr);
                    
                SELECT LAST_INSERT_ID() INTO @newBookId;
                
                INSERT INTO books_authors (`book_id`, `author_id`) SELECT @newBookId, author_id FROM books_authors_bckp WHERE book_id=_id;
                INSERT INTO book_notation (`id`, `book_id`) SELECT id, @newBookId FROM book_notation_bckp WHERE book_id=_id;
                INSERT INTO book_group (`book_id`, `referenceGroup_id`) SELECT @newBookId, referenceGroup_id FROM book_group_bckp WHERE book_id=_id;
                    
                IF (_discr = 'paperbook') THEN
                    INSERT INTO paper_book (`id`) VALUES (@newBookId);
                END IF; 
                
                IF (_discr = 'electronicbook') THEN
                    -- The id of the original electronic book == the file id
                    INSERT INTO electronic_book (`id`, `book_file_id`) VALUES (@newBookId, _electronic_book_id);
                END IF;
                
            END LOOP booksLoop;
            CLOSE books_cursor;
                
        END;
EOT;


        $this->addSql("DROP PROCEDURE IF EXISTS version20191102162259;");
        $this->addSql($procedure);
        $this->addSql("CALL version20191102162259();");

//        $this->addSql($query);
//        $this->addSql('CALL version20191102162259()');
//        $this->addSql('INSERT INTO book ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

//        $this->addSql('ALTER TABLE book ADD _electronic_book_id INT DEFAULT NULL, ADD _paper_book_id INT DEFAULT NULL, DROP discr');
//        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3311606E160 FOREIGN KEY (_paper_book_id) REFERENCES paper_book (id)');
//        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331AC578236 FOREIGN KEY (_electronic_book_id) REFERENCES electronic_book (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBE5A331AC578236 ON book (_electronic_book_id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBE5A3311606E160 ON book (_paper_book_id)');
//        $this->addSql('ALTER TABLE electronic_book DROP FOREIGN KEY FK_7602E9E3BF396750');
//        $this->addSql('ALTER TABLE electronic_book CHANGE id id INT AUTO_INCREMENT NOT NULL');
//        $this->addSql('ALTER TABLE electronic_book ADD CONSTRAINT FK_7602E9E3BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE paper_book DROP FOREIGN KEY FK_3D0D0226BF396750');
//        $this->addSql('ALTER TABLE paper_book CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
