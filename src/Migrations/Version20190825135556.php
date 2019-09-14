<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190825135556 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, note DOUBLE PRECISION NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_268BC95A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_notation (id INT NOT NULL, book_id INT DEFAULT NULL, INDEX IDX_1B97AACC16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE book_notation ADD CONSTRAINT FK_1B97AACC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_notation ADD CONSTRAINT FK_1B97AACCBF396750 FOREIGN KEY (id) REFERENCES notation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_notation DROP FOREIGN KEY FK_1B97AACCBF396750');
        $this->addSql('DROP TABLE notation');
        $this->addSql('DROP TABLE book_notation');
    }
}
