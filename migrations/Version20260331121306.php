<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260331121306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD company_depot_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6499D87C1B3 FOREIGN KEY (company_depot_id) REFERENCES company_depot (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6499D87C1B3 ON "user" (company_depot_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6499D87C1B3');
        $this->addSql('DROP INDEX IDX_8D93D6499D87C1B3');
        $this->addSql('ALTER TABLE "user" DROP company_depot_id');
    }
}
