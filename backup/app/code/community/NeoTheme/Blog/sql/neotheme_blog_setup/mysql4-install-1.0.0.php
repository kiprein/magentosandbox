<?php
/**
 * NeoTheme (Neo Industries Pty Ltd)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Neo Industries Pty LTD Non-Distributable Software Modification License (NDSML)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.neotheme.com/legal/licenses/NDSM.html
 * If the license is not included with the package or for any other reason, 
 * you did not receive your licence please send an email to 
 * license@neotheme.com so we can send you a copy immediately.
 *
 * This software comes with no warrenty of any kind. By Using this software, the user agrees to hold 
 * Neo Industries Pty Ltd harmless of any damage it may cause.
 *
 * @category    modules
 * @module      NeoTheme_Blog
 * @copyright   Copyright (c) 2011 Neo Industries Pty Ltd (http://www.neotheme.com)
 * @license     http://www.neotheme.com/  Non-Distributable Software Modification License(NDSML 1.0)
 */
$installer = $this;
$installer->startSetup();
if (!version_compare(Mage::getVersion(), '1.6', '>=')) {
    $installer->run(
        "DROP TABLE IF EXISTS {$this->getTable('neotheme_blog/post')};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {$this->getTable('neotheme_blog/post')} (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Updated At',
  `status` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Defines Is Entity Active',
  `title` text NOT NULL COMMENT 'Title',
  `summary` text NOT NULL COMMENT 'Post Content',
  `content_html` text NOT NULL COMMENT 'Post Content',
  `meta_description` text COMMENT 'Meta Description',
  `meta_title` text COMMENT 'Meta Title',
  `meta_keywords` text COMMENT 'Meta Keywords',
  `store_ids` text NOT NULL COMMENT 'Store IDs',
  `category_ids` text NOT NULL COMMENT 'Category IDs',
  `tag_ids` text NOT NULL COMMENT 'Category IDs',
  `author` text,
  `post_date` timestamp NULL DEFAULT NULL,
  `cms_identifier` varchar(255) DEFAULT NULL,
  `customer_group_ids` text,
  `publish_date` timestamp NULL DEFAULT NULL,
  `use_summary` smallint(6) DEFAULT '1',
  `root_template` varchar(255) DEFAULT NULL,
  `layout_update_xml` text,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `IDX_CMS_IDENTIFIER` (`cms_identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='neotheme_blog_post';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS  {$this->getTable('neotheme_blog/category')};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {$this->getTable('neotheme_blog/category')} (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
  `parent_id` int(10) unsigned NOT NULL COMMENT 'Parent Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Updated At',
  `status` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Defines Is Entity Active',
  `name` text NOT NULL COMMENT 'Post Content',
  `summary` text NOT NULL COMMENT 'Post Content',
  `store_ids` text NOT NULL COMMENT 'Store IDs',
  `cms_identifier` varchar(255) DEFAULT NULL,
  `root_template` varchar(255) DEFAULT NULL,
  `layout_update_xml` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` text,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `IDX_CMS_IDENTIFIER` (`cms_identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='neotheme_blog_category';
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS {$this->getTable('neotheme_blog/tag')};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {$this->getTable('neotheme_blog/tag')} (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Updated At',
  `status` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Defines Is Entity Active',
  `name` text NOT NULL COMMENT 'Post Content',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='neotheme_blog_tag';
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS {$this->getTable('neotheme_blog/comment')};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {$this->getTable('neotheme_blog/comment')} (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Created At',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Updated At',
  `status` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Defines Is Entity Active',
  `post_id` int(10) unsigned NOT NULL COMMENT 'Post that this comment is about',
  `username` text NOT NULL COMMENT 'Post Content',
  `name` text NOT NULL COMMENT 'Post Content',
  `comment` text NOT NULL COMMENT 'Post Content',
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='neotheme_blog_comment';
/*!40101 SET character_set_client = @saved_cs_client */;
");
}
else{
$table = $installer->getConnection()
        ->newTable($this->getTable('neotheme_blog/post'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Entity Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '1',
        ), 'Defines Is Entity Active')
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Title')
        ->addColumn('author', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Author')
        ->addColumn('post_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Post Date To Display')
        ->addColumn('summary', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content')
        ->addColumn('content_html', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content')
        ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => '',
        ), 'Meta Description')
        ->addColumn('meta_title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => '',
        ), 'Meta Title')
        ->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => '',
        ), 'Meta Keywords')
        ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Store IDs')
        ->addColumn('category_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Category IDs')
        ->addColumn('tag_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Category IDs');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
        ->newTable($this->getTable('neotheme_blog/category'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Entity Id')
        ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Parent Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '1',
        ), 'Defines Is Entity Active')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content')
        ->addColumn('summary', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content')
        ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Store IDs');
$installer->getConnection()->createTable($table);


$table = $installer->getConnection()
        ->newTable($this->getTable('neotheme_blog/tag'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Entity Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '1',
        ), 'Defines Is Entity Active')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
        ->newTable($this->getTable('neotheme_blog/comment'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Entity Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
        ), 'Updated At')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '1',
        ), 'Defines Is Entity Active')
        ->addColumn('post_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Post that this comment is about')
        ->addColumn('username', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content')
        ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
            'default' => '',
        ), 'Post Content');
$installer->getConnection()->createTable($table);
}
$installer->endSetup();
