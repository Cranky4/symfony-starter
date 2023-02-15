<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Persistence\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215130557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create news table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE news (
            id UUID NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            content TEXT NOT NULL, 
            short VARCHAR(255) NOT NULL, 
            day DATE NOT NULL, 
            time VARCHAR(255) NOT NULL, 
            image VARCHAR(255) DEFAULT NULL, 
            source_name VARCHAR(255) DEFAULT NULL, 
            source_id VARCHAR(255) DEFAULT NULL, 
            
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX UQ_news_source ON news (source_name, source_id)');
        $this->addSql('CREATE INDEX IN_news_day ON news (day)');
        $this->addSql('COMMENT ON COLUMN news.day IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE news');
    }
}
