<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221029120312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_on_platform ADD game_id INT NOT NULL, ADD platform_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_on_platform ADD CONSTRAINT FK_484B24D6E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_on_platform ADD CONSTRAINT FK_484B24D6FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id)');
        $this->addSql('CREATE INDEX IDX_484B24D6E48FD905 ON game_on_platform (game_id)');
        $this->addSql('CREATE INDEX IDX_484B24D6FFE6496F ON game_on_platform (platform_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_on_platform DROP FOREIGN KEY FK_484B24D6E48FD905');
        $this->addSql('ALTER TABLE game_on_platform DROP FOREIGN KEY FK_484B24D6FFE6496F');
        $this->addSql('DROP INDEX IDX_484B24D6E48FD905 ON game_on_platform');
        $this->addSql('DROP INDEX IDX_484B24D6FFE6496F ON game_on_platform');
        $this->addSql('ALTER TABLE game_on_platform DROP game_id, DROP platform_id');
    }
}
