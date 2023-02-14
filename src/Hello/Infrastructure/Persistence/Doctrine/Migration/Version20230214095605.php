<?php

declare(strict_types=1);

namespace App\Hello\Infrastructure\Persistence\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214095605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE hellos (
            id UUID NOT NULL, message VARCHAR(255) DEFAULT NULL, 
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN hellos.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE hellos');
    }
}
