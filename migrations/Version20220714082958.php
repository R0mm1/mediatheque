<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220714082958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix fk between book and cover';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331922726E9');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331922726E9 FOREIGN KEY (cover_id) REFERENCES cover (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331922726E9');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331922726E9 FOREIGN KEY (cover_id) REFERENCES file (id)');
    }
}
