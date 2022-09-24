<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220915134728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) NOT NULL, birth_year VARCHAR(4) DEFAULT NULL, death_year VARCHAR(4) DEFAULT NULL, biography LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE author ADD person_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE author ADD CONSTRAINT FK_BDAFD8C8217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE author CHANGE lastname lastname VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDAFD8C8217BBB47 ON author (person_id)');
        $this->write("Don't forget to launch migration:author2person now (\App\Command\Author2PersonMigrationCommand)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author DROP FOREIGN KEY FK_BDAFD8C8217BBB47');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP INDEX UNIQ_BDAFD8C8217BBB47 ON author');
        $this->addSql('ALTER TABLE author DROP person_id');
    }
}
