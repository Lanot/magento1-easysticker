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
$stickerProductTable = $this->getTable('lanot_easysticker/sticker_product');
$productTable = $this->getTable('catalog/product');

//create table for sticker
$this->run("
	DROP TABLE IF EXISTS `{$stickerProductTable}`;
	CREATE TABLE `{$stickerProductTable}` (
	    `product_id`   int(10) unsigned NOT NULL COMMENT 'Product ID',
	    `sticker_id`   int(10) unsigned NOT NULL COMMENT 'Sticker ID',
		PRIMARY KEY (`product_id`, `sticker_id`),
  		CONSTRAINT `FK_STICKER_PRODUCT_ID_CATALOG_PRODUCT_ENTITY_ID` FOREIGN KEY (`product_id`) REFERENCES `{$productTable}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  		CONSTRAINT `FK_STICKER_STICKER_ID_STICKER_STICKER_ID` FOREIGN KEY (`sticker_id`) REFERENCES `{$stickerTable}` (`sticker_id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stickers To Products Table';
");

$this->endSetup();