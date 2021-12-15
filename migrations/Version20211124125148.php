<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211124125148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, commandeur_id INT NOT NULL, datecommande DATETIME NOT NULL, prixtotal DOUBLE PRECISION NOT NULL, modepaiement VARCHAR(255) NOT NULL, etatpaiement INT NOT NULL, datelivraison DATETIME NOT NULL, nbrmenu INT NOT NULL, lieulivraison VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_6EEAA67D996F9D6F (commandeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D996F9D6F FOREIGN KEY (commandeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE food ADD menucommandeur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7BC360E63 FOREIGN KEY (menucommandeur_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_D43829F7BC360E63 ON food (menucommandeur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7BC360E63');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP INDEX IDX_D43829F7BC360E63 ON food');
        $this->addSql('ALTER TABLE food DROP menucommandeur_id');
    }
}
