<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519023530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE event CHANGE image image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C84955FD02F13 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation CHANGE evenement_id event_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C8495571F7E88B FOREIGN KEY (event_id) REFERENCES event (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C8495571F7E88B ON reservation (event_id)
        SQL);
        // Pour les réservations existantes sans événement
    $this->addSql("UPDATE reservation SET event_id = [ID_D_UN_EVENEMENT_EXISTANT] WHERE event_id IS NULL");
    
    // Pour les réservations existantes sans utilisateur
    $this->addSql("UPDATE reservation SET utilisateur_id = [ID_D_UN_UTILISATEUR_EXISTANT] WHERE utilisateur_id IS NULL");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE event CHANGE image image VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495571F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C8495571F7E88B ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation CHANGE event_id evenement_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955FD02F13 ON reservation (evenement_id)
        SQL);
    }
}
