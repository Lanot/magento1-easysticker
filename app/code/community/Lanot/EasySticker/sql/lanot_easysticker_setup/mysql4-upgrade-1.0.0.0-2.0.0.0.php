<?php
/**
 * Private Entrepreneur Anatolii Lehkyi (aka Lanot)
 *
 * @category    Lanot
 * @package     Lanot_EasySticker
 * @copyright   Copyright (c) 2010 Anatolii Lehkyi
 * @license     http://opensource.org/licenses/osl-3.0.php
 * @link        http://www.lanot.biz/
 */

/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$this->startSetup();

//tables definition
$stickerTable = $this->getTable('lanot_easysticker/sticker');

//create table for sticker
$this->run("
	DROP TABLE IF EXISTS `{$stickerTable}`;
	CREATE TABLE `{$stickerTable}` (
	    `sticker_id`   int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Sticker ID',
	    `is_active`  tinyint DEFAULT 0,
	    `title`      varchar(255) DEFAULT NULL,

	    `opacity`    int DEFAULT 0,
        `image`      varchar(255) DEFAULT NULL,
        `position`   varchar(32),

	    `image_size_image`       varchar(16) DEFAULT null,
	    `image_size_thumbnail`   varchar(16) DEFAULT null,
	    `image_size_small_image` varchar(16) DEFAULT null,

		`created_at` timestamp NULL DEFAULT NULL COMMENT 'Creation Time',
  		`updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Update Time',
		PRIMARY KEY (`sticker_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stickers Entity Table';
");

$this->endSetup();