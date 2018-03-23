<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180323101543 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE lock_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE account_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE operation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE lock (id INT NOT NULL, source_id INT DEFAULT NULL, destination_id INT DEFAULT NULL, amount INT NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, approved TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_878F9B0E953C1C61 ON lock (source_id)');
        $this->addSql('CREATE INDEX IDX_878F9B0E816C6140 ON lock (destination_id)');
        $this->addSql('CREATE TABLE account (id INT NOT NULL, username VARCHAR(255) NOT NULL, busy_by_pid INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4F85E0677 ON account (username)');
        $this->addSql('CREATE TABLE operation (id INT NOT NULL, account_id INT NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1981A66D9B6B5FBA ON operation (account_id)');
        $this->addSql('ALTER TABLE lock ADD CONSTRAINT FK_878F9B0E953C1C61 FOREIGN KEY (source_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lock ADD CONSTRAINT FK_878F9B0E816C6140 FOREIGN KEY (destination_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lock ADD CONSTRAINT lock_check_source_dest_notnull CHECK(source_id IS NOT NULL OR destination_id IS NOT NULL)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lock DROP CONSTRAINT lock_check_source_dest_notnull');
        $this->addSql('ALTER TABLE lock DROP CONSTRAINT FK_878F9B0E953C1C61');
        $this->addSql('ALTER TABLE lock DROP CONSTRAINT FK_878F9B0E816C6140');
        $this->addSql('ALTER TABLE operation DROP CONSTRAINT FK_1981A66D9B6B5FBA');
        $this->addSql('DROP SEQUENCE lock_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE account_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE operation_id_seq CASCADE');
        $this->addSql('DROP TABLE lock');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE operation');
    }
}
