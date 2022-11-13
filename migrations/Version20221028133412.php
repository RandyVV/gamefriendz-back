<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028133412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_owns_gameonplatform (player_id INT NOT NULL, game_on_platform_id INT NOT NULL, INDEX IDX_615145E499E6F5DF (player_id), INDEX IDX_615145E4FFA3F544 (game_on_platform_id), PRIMARY KEY(player_id, game_on_platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_wantstoplay_gameonplatform (player_id INT NOT NULL, game_on_platform_id INT NOT NULL, INDEX IDX_D52183B99E6F5DF (player_id), INDEX IDX_D52183BFFA3F544 (game_on_platform_id), PRIMARY KEY(player_id, game_on_platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_owns_gameonplatform ADD CONSTRAINT FK_615145E499E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_owns_gameonplatform ADD CONSTRAINT FK_615145E4FFA3F544 FOREIGN KEY (game_on_platform_id) REFERENCES game_on_platform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_wantstoplay_gameonplatform ADD CONSTRAINT FK_D52183B99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_wantstoplay_gameonplatform ADD CONSTRAINT FK_D52183BFFA3F544 FOREIGN KEY (game_on_platform_id) REFERENCES game_on_platform (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_owns_gameonplatform DROP FOREIGN KEY FK_615145E499E6F5DF');
        $this->addSql('ALTER TABLE player_owns_gameonplatform DROP FOREIGN KEY FK_615145E4FFA3F544');
        $this->addSql('ALTER TABLE player_wantstoplay_gameonplatform DROP FOREIGN KEY FK_D52183B99E6F5DF');
        $this->addSql('ALTER TABLE player_wantstoplay_gameonplatform DROP FOREIGN KEY FK_D52183BFFA3F544');
        $this->addSql('DROP TABLE player_owns_gameonplatform');
        $this->addSql('DROP TABLE player_wantstoplay_gameonplatform');
    }
}
