<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220330081633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders ADD cart_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE1AD5CDBF ON orders (cart_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `order`');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE1AD5CDBF');
        $this->addSql('DROP INDEX UNIQ_E52FFDEE1AD5CDBF ON orders');
        $this->addSql('ALTER TABLE orders DROP cart_id');
    }
}
