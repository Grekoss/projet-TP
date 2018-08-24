<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180723132738 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, user_id INT NOT NULL, description LONGTEXT NOT NULL, date_at DATETIME NOT NULL, INDEX IDX_9474526C71F7E88B (event_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, number VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_CD1DE18A98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_event (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, organize_id INT NOT NULL, visibility_id INT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(10) NOT NULL, city VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, price INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, date_at DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(50) NOT NULL, participants_limit INT NOT NULL, time_at TIME NOT NULL, join_time_limit DATETIME NOT NULL, min_age INT NOT NULL, max_age INT NOT NULL, INDEX IDX_ED7D876AAE80F5DF (department_id), INDEX IDX_ED7D876AACBC40A8 (organize_id), INDEX IDX_ED7D876AB7157780 (visibility_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_tag (event_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_1246725071F7E88B (event_id), INDEX IDX_12467250BAD26311 (tag_id), PRIMARY KEY(event_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_reporting (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, user_id INT NOT NULL, description LONGTEXT NOT NULL, is_manage TINYINT(1) NOT NULL, date_at DATETIME NOT NULL, INDEX IDX_3BE9C4A271F7E88B (event_id), INDEX IDX_3BE9C4A2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE following (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_71BF8DE371F7E88B (event_id), INDEX IDX_71BF8DE3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, sendee_id INT NOT NULL, title VARCHAR(100) NOT NULL, body LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, INDEX IDX_BF5476CA3BEADCBB (sendee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, participant_id INT DEFAULT NULL, event_id INT DEFAULT NULL, created_at DATETIME NOT NULL, is_real TINYINT(1) NOT NULL, has_rated TINYINT(1) NOT NULL, INDEX IDX_AB55E24F9D1C3019 (participant_id), INDEX IDX_AB55E24F71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relation_ship (id INT AUTO_INCREMENT NOT NULL, user_main_id INT NOT NULL, link_id INT NOT NULL, user_concerned_id INT NOT NULL, INDEX IDX_4D1AE8A93A73B980 (user_main_id), INDEX IDX_4D1AE8A9ADA40271 (link_id), INDEX IDX_4D1AE8A960AD6A75 (user_concerned_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, background_color VARCHAR(30) DEFAULT NULL, text_color VARCHAR(30) DEFAULT NULL, slug VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, role_id INT NOT NULL, genre_id INT NOT NULL, username VARCHAR(50) NOT NULL, lastname VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, address VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(5) NOT NULL, rating INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, eval_count INT NOT NULL, is_mailing TINYINT(1) DEFAULT NULL, connected_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_88BDF3E9F85E0677 (username), UNIQUE INDEX UNIQ_88BDF3E9E7927C74 (email), INDEX IDX_88BDF3E9AE80F5DF (department_id), INDEX IDX_88BDF3E9D60322AC (role_id), INDEX IDX_88BDF3E94296D31F (genre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_reporting (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, accused_user_id INT NOT NULL, description LONGTEXT NOT NULL, is_manage TINYINT(1) NOT NULL, date_at DATETIME NOT NULL, INDEX IDX_BD9C777BA76ED395 (user_id), INDEX IDX_BD9C777B34FFB185 (accused_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visibility (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C71F7E88B FOREIGN KEY (event_id) REFERENCES app_event (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE app_event ADD CONSTRAINT FK_ED7D876AAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE app_event ADD CONSTRAINT FK_ED7D876AACBC40A8 FOREIGN KEY (organize_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_event ADD CONSTRAINT FK_ED7D876AB7157780 FOREIGN KEY (visibility_id) REFERENCES visibility (id)');
        $this->addSql('ALTER TABLE event_tag ADD CONSTRAINT FK_1246725071F7E88B FOREIGN KEY (event_id) REFERENCES app_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_tag ADD CONSTRAINT FK_12467250BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_reporting ADD CONSTRAINT FK_3BE9C4A271F7E88B FOREIGN KEY (event_id) REFERENCES app_event (id)');
        $this->addSql('ALTER TABLE event_reporting ADD CONSTRAINT FK_3BE9C4A2A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE371F7E88B FOREIGN KEY (event_id) REFERENCES app_event (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3BEADCBB FOREIGN KEY (sendee_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F9D1C3019 FOREIGN KEY (participant_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F71F7E88B FOREIGN KEY (event_id) REFERENCES app_event (id)');
        $this->addSql('ALTER TABLE relation_ship ADD CONSTRAINT FK_4D1AE8A93A73B980 FOREIGN KEY (user_main_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE relation_ship ADD CONSTRAINT FK_4D1AE8A9ADA40271 FOREIGN KEY (link_id) REFERENCES link (id)');
        $this->addSql('ALTER TABLE relation_ship ADD CONSTRAINT FK_4D1AE8A960AD6A75 FOREIGN KEY (user_concerned_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9D60322AC FOREIGN KEY (role_id) REFERENCES app_role (id)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E94296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE user_reporting ADD CONSTRAINT FK_BD9C777BA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE user_reporting ADD CONSTRAINT FK_BD9C777B34FFB185 FOREIGN KEY (accused_user_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_event DROP FOREIGN KEY FK_ED7D876AAE80F5DF');
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E9AE80F5DF');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C71F7E88B');
        $this->addSql('ALTER TABLE event_tag DROP FOREIGN KEY FK_1246725071F7E88B');
        $this->addSql('ALTER TABLE event_reporting DROP FOREIGN KEY FK_3BE9C4A271F7E88B');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE371F7E88B');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F71F7E88B');
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E94296D31F');
        $this->addSql('ALTER TABLE relation_ship DROP FOREIGN KEY FK_4D1AE8A9ADA40271');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A98260155');
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E9D60322AC');
        $this->addSql('ALTER TABLE event_tag DROP FOREIGN KEY FK_12467250BAD26311');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE app_event DROP FOREIGN KEY FK_ED7D876AACBC40A8');
        $this->addSql('ALTER TABLE event_reporting DROP FOREIGN KEY FK_3BE9C4A2A76ED395');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE3A76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA3BEADCBB');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F9D1C3019');
        $this->addSql('ALTER TABLE relation_ship DROP FOREIGN KEY FK_4D1AE8A93A73B980');
        $this->addSql('ALTER TABLE relation_ship DROP FOREIGN KEY FK_4D1AE8A960AD6A75');
        $this->addSql('ALTER TABLE user_reporting DROP FOREIGN KEY FK_BD9C777BA76ED395');
        $this->addSql('ALTER TABLE user_reporting DROP FOREIGN KEY FK_BD9C777B34FFB185');
        $this->addSql('ALTER TABLE app_event DROP FOREIGN KEY FK_ED7D876AB7157780');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE app_event');
        $this->addSql('DROP TABLE event_tag');
        $this->addSql('DROP TABLE event_reporting');
        $this->addSql('DROP TABLE following');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE relation_ship');
        $this->addSql('DROP TABLE app_role');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE user_reporting');
        $this->addSql('DROP TABLE visibility');
    }
}
