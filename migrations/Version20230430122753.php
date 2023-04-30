<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230430122753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F13933E7B');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F84A0A3ED');
        $this->addSql('DROP INDEX IDX_B6BD307F13933E7B ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F84A0A3ED ON message');
        $this->addSql('ALTER TABLE message ADD author_id INT DEFAULT NULL, ADD conversation_id INT DEFAULT NULL, ADD status TINYINT NOT NULL, DROP send_id, DROP content_id, DROP see');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF675F31B ON message (author_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF675F31B');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('DROP INDEX IDX_B6BD307FF675F31B ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396 ON message');
        $this->addSql('ALTER TABLE message ADD send_id INT DEFAULT NULL, ADD content_id INT DEFAULT NULL, ADD see TINYINT(1) NOT NULL, DROP author_id, DROP conversation_id, DROP status');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F13933E7B FOREIGN KEY (send_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F84A0A3ED FOREIGN KEY (content_id) REFERENCES conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F13933E7B ON message (send_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F84A0A3ED ON message (content_id)');
    }
}
