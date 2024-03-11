<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311142228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant_campaign (participant_id INT NOT NULL, campaign_id INT NOT NULL, INDEX IDX_115497209D1C3019 (participant_id), INDEX IDX_11549720F639F774 (campaign_id), PRIMARY KEY(participant_id, campaign_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participant_campaign ADD CONSTRAINT FK_115497209D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant_campaign ADD CONSTRAINT FK_11549720F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX fk_participant_campaign1_idx ON participant');
        $this->addSql('ALTER TABLE participant DROP campaign_id');
        $this->addSql('ALTER TABLE payment DROP INDEX fk_payment_participant1_idx, ADD UNIQUE INDEX UNIQ_6D28840D9D1C3019 (participant_id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant_campaign DROP FOREIGN KEY FK_115497209D1C3019');
        $this->addSql('ALTER TABLE participant_campaign DROP FOREIGN KEY FK_11549720F639F774');
        $this->addSql('DROP TABLE participant_campaign');
        $this->addSql('ALTER TABLE participant ADD campaign_id VARCHAR(32) NOT NULL');
        $this->addSql('CREATE INDEX fk_participant_campaign1_idx ON participant (campaign_id)');
        $this->addSql('ALTER TABLE payment DROP INDEX UNIQ_6D28840D9D1C3019, ADD INDEX fk_payment_participant1_idx (participant_id)');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9D1C3019');
    }
}
