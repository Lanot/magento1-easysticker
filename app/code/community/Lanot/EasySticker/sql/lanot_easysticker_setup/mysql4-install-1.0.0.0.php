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

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'lanot_sticker_id',
    array(
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,

        'input'         => 'multiselect',
        'type'          => 'text',
        'label'         => 'Product Stickers',
        'source'        => 'lanot_easysticker/product_attribute_source_sticker',
        'backend'       => 'lanot_easysticker/product_attribute_backend_sticker',

		'used_in_product_listing' => true, //add to collection
        'visible'           => true, //add to admin UI
        'visible_on_front'  => false,
        'required'          => false,
        'user_defined'      => false,
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'unique'            => false,
    ));

$installer->endSetup();