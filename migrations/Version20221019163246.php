<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019163246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reference_group_book (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', book_id INT NOT NULL, reference_group_id INT NOT NULL, position INT NOT NULL, INDEX IDX_B6A1CD0E16A2B381 (book_id), INDEX IDX_B6A1CD0E265E8238 (reference_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reference_group_book ADD CONSTRAINT FK_B6A1CD0E16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE reference_group_book ADD CONSTRAINT FK_B6A1CD0E265E8238 FOREIGN KEY (reference_group_id) REFERENCES reference_group (id)');
        $this->write("Don't forget to launch migration:referenceGroupBook now (\App\Command\ReferenceGroupBookMigrationCommand)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reference_group_book DROP FOREIGN KEY FK_B6A1CD0E16A2B381');
        $this->addSql('ALTER TABLE reference_group_book DROP FOREIGN KEY FK_B6A1CD0E265E8238');
        $this->addSql('DROP TABLE reference_group_book');
    }
}
