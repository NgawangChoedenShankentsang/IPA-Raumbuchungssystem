<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423132743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE telefon_box (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, status_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, INDEX IDX_63AE00669D86650F (user_id_id), INDEX IDX_63AE0066881ECFA7 (status_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE telefon_box ADD CONSTRAINT FK_63AE00669D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE telefon_box ADD CONSTRAINT FK_63AE0066881ECFA7 FOREIGN KEY (status_id_id) REFERENCES status (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD company_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D64938B53C32 FOREIGN KEY (company_id_id) REFERENCES company (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D64938B53C32 ON user (company_id_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D64938B53C32
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE telefon_box DROP FOREIGN KEY FK_63AE00669D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE telefon_box DROP FOREIGN KEY FK_63AE0066881ECFA7
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE status
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE telefon_box
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8D93D64938B53C32 ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP company_id_id
        SQL);
    }
}
