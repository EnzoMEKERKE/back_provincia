<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261125140243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add course and registration fields while preserving existing users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, niveau VARCHAR(255) NOT NULL, difficulte VARCHAR(255) NOT NULL, langage VARCHAR(255) NOT NULL, duree INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user CHANGE name nom VARCHAR(255) NOT NULL, CHANGE surname prenom VARCHAR(255) NOT NULL, ADD pseudo VARCHAR(255) DEFAULT NULL, ADD niveau INT NOT NULL DEFAULT 1, MODIFY role VARCHAR(50) NOT NULL DEFAULT \'ROLE_USER\', MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql("UPDATE user SET pseudo = LEFT(SUBSTRING_INDEX(email, '@', 1), 255) WHERE pseudo IS NULL");
        $this->addSql('ALTER TABLE user MODIFY pseudo VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE cours');
        $this->addSql('ALTER TABLE user CHANGE nom name VARCHAR(255) NOT NULL, CHANGE prenom surname VARCHAR(255) NOT NULL, DROP pseudo, DROP niveau, MODIFY role VARCHAR(50) NOT NULL, MODIFY created_at DATETIME NOT NULL');
    }
}
