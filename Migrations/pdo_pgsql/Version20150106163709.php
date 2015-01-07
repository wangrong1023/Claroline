<?php

namespace Icap\BlogBundle\Migrations\pdo_pgsql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2015/01/06 04:37:10
 */
class Version20150106163709 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE INDEX IDX_FD75E6C4B87FAB32 ON icap__blog (resourceNode_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D1AAC984DAE07E97 ON icap__blog_options (blog_id)
        ");
        $this->addSql("
            ALTER TABLE icap__blog_widget_list_blog RENAME COLUMN blog_id TO resourceNode_id
        ");
        $this->addSql("
            ALTER TABLE icap__blog_widget_list_blog 
            ADD CONSTRAINT FK_294D4E02B87FAB32 FOREIGN KEY (resourceNode_id) 
            REFERENCES claro_resource_node (id) 
            ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        ");
        $this->addSql("
            CREATE INDEX IDX_294D4E02B87FAB32 ON icap__blog_widget_list_blog (resourceNode_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP INDEX IDX_FD75E6C4B87FAB32
        ");
        $this->addSql("
            DROP INDEX IDX_D1AAC984DAE07E97
        ");
        $this->addSql("
            ALTER TABLE icap__blog_widget_list_blog 
            DROP CONSTRAINT FK_294D4E02B87FAB32
        ");
        $this->addSql("
            DROP INDEX IDX_294D4E02B87FAB32
        ");
        $this->addSql("
            ALTER TABLE icap__blog_widget_list_blog RENAME COLUMN resourcenode_id TO blog_id
        ");
    }
}