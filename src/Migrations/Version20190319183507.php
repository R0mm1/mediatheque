<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190319183507 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE paper_book ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE paper_book ADD CONSTRAINT FK_3D0D02267E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_3D0D02267E3C61F9 ON paper_book (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE paper_book DROP FOREIGN KEY FK_3D0D02267E3C61F9');
        $this->addSql('DROP INDEX IDX_3D0D02267E3C61F9 ON paper_book');
        $this->addSql('ALTER TABLE paper_book DROP owner_id');
    }
}
