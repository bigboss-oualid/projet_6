<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200508010857 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, username VARCHAR(25) NOT NULL, email VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649D1B862B8 (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE picture ADD user_id INT DEFAULT NULL, ADD old_name VARCHAR(255) NOT NULL, ADD filename VARCHAR(255) NOT NULL, DROP title, DROP alt, DROP path, CHANGE trick_id trick_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F89A76ED395 ON picture (user_id)');
        $this->addSql('ALTER TABLE trick ADD author_id INT NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91E2B36786B ON trick (title)');
        $this->addSql('CREATE INDEX IDX_D8F0A91EF675F31B ON trick (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89A76ED395');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EF675F31B');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_16DB4F89A76ED395 ON picture');
        $this->addSql('ALTER TABLE picture ADD title VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD alt VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP user_id, DROP old_name, DROP filename, CHANGE trick_id trick_id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_D8F0A91E2B36786B ON trick');
        $this->addSql('DROP INDEX IDX_D8F0A91EF675F31B ON trick');
        $this->addSql('ALTER TABLE trick DROP author_id, DROP updated_at');
    }
}
