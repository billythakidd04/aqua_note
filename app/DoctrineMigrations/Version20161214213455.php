<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161214213455 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genus ADD sub_family_id INT NOT NULL, DROP sub_family');
        $this->addSql('ALTER TABLE genus ADD CONSTRAINT FK_38C5106ED15310D4 FOREIGN KEY (sub_family_id) REFERENCES sub_family (id)');
        $this->addSql('CREATE INDEX IDX_38C5106ED15310D4 ON genus (sub_family_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genus DROP FOREIGN KEY FK_38C5106ED15310D4');
        $this->addSql('DROP INDEX IDX_38C5106ED15310D4 ON genus');
        $this->addSql('ALTER TABLE genus ADD sub_family VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP sub_family_id');
    }
}
