<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220823024917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE characters_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE movies_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE characters (id INT NOT NULL, name VARCHAR(255) NOT NULL, mass VARCHAR(255) DEFAULT NULL, height VARCHAR(255) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A29410E5E237E06 ON characters (name)');
        $this->addSql('CREATE TABLE movies_characters (character_id INT NOT NULL, movie_id INT NOT NULL, PRIMARY KEY(character_id, movie_id))');
        $this->addSql('CREATE INDEX IDX_6BDFABF81136BE75 ON movies_characters (character_id)');
        $this->addSql('CREATE INDEX IDX_6BDFABF88F93B6FC ON movies_characters (movie_id)');
        $this->addSql('CREATE TABLE movies (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C61EED305E237E06 ON movies (name)');
        $this->addSql('ALTER TABLE movies_characters ADD CONSTRAINT FK_6BDFABF81136BE75 FOREIGN KEY (character_id) REFERENCES characters (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE movies_characters ADD CONSTRAINT FK_6BDFABF88F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE characters_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE movies_id_seq CASCADE');
        $this->addSql('ALTER TABLE movies_characters DROP CONSTRAINT FK_6BDFABF81136BE75');
        $this->addSql('ALTER TABLE movies_characters DROP CONSTRAINT FK_6BDFABF88F93B6FC');
        $this->addSql('DROP TABLE characters');
        $this->addSql('DROP TABLE movies_characters');
        $this->addSql('DROP TABLE movies');
    }
}
