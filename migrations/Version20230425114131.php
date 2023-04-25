<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425114131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversation_player (conversation_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_AAD5C0A59AC0396 (conversation_id), INDEX IDX_AAD5C0A599E6F5DF (player_id), PRIMARY KEY(conversation_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conversation_player ADD CONSTRAINT FK_AAD5C0A59AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_player ADD CONSTRAINT FK_AAD5C0A599E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation_player DROP FOREIGN KEY FK_AAD5C0A59AC0396');
        $this->addSql('ALTER TABLE conversation_player DROP FOREIGN KEY FK_AAD5C0A599E6F5DF');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE conversation_player');
    }
}
