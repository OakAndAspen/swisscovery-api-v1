<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190128113238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, point_of_interest_id INT NOT NULL, text LONGTEXT DEFAULT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', is_interested TINYINT(1) NOT NULL, has_visited TINYINT(1) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C1FE9DE17 (point_of_interest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point_of_interest (id INT AUTO_INCREMENT NOT NULL, feature_id INT NOT NULL, x DOUBLE PRECISION NOT NULL, y DOUBLE PRECISION NOT NULL, label VARCHAR(255) NOT NULL, canton VARCHAR(2) NOT NULL, commune VARCHAR(255) NOT NULL, wiki_title VARCHAR(255) DEFAULT NULL, wiki_link VARCHAR(1000) DEFAULT NULL, category VARCHAR(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(1000) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1FE9DE17 FOREIGN KEY (point_of_interest_id) REFERENCES point_of_interest (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C1FE9DE17');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE point_of_interest');
        $this->addSql('DROP TABLE user');
    }
}
