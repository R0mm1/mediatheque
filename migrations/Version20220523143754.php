<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220523143754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE electronic_book_information (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', book_file_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_93FB8C25EB51FA (book_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE electronic_book_information_electronic_book_information_image (electronic_book_information_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', electronic_book_information_image_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_9C2C792E59EBD7B (electronic_book_information_id), UNIQUE INDEX UNIQ_9C2C792221C3DBA (electronic_book_information_image_id), PRIMARY KEY(electronic_book_information_id, electronic_book_information_image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE electronic_book_information_book (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE electronic_book_information_image (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE electronic_book_information ADD CONSTRAINT FK_93FB8C25EB51FA FOREIGN KEY (book_file_id) REFERENCES electronic_book_information_book (id)');
        $this->addSql('ALTER TABLE electronic_book_information_electronic_book_information_image ADD CONSTRAINT FK_9C2C792E59EBD7B FOREIGN KEY (electronic_book_information_id) REFERENCES electronic_book_information (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE electronic_book_information_electronic_book_information_image ADD CONSTRAINT FK_9C2C792221C3DBA FOREIGN KEY (electronic_book_information_image_id) REFERENCES electronic_book_information_image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE electronic_book_information_book ADD CONSTRAINT FK_DB933F35BF396750 FOREIGN KEY (id) REFERENCES file (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE electronic_book_information_electronic_book_information_image DROP FOREIGN KEY FK_9C2C792E59EBD7B');
        $this->addSql('ALTER TABLE electronic_book_information DROP FOREIGN KEY FK_93FB8C25EB51FA');
        $this->addSql('ALTER TABLE electronic_book_information_electronic_book_information_image DROP FOREIGN KEY FK_9C2C792221C3DBA');
        $this->addSql('DROP TABLE electronic_book_information');
        $this->addSql('DROP TABLE electronic_book_information_electronic_book_information_image');
        $this->addSql('DROP TABLE electronic_book_information_book');
        $this->addSql('DROP TABLE electronic_book_information_image');
    }
}
