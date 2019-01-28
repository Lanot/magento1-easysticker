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

/** @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
/** @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $this->getConnection();

/** @var $stickerModel Lanot_EasySticker_Model_Sticker */
$stickerModel = Mage::getModel('lanot_easysticker/sticker');

/** @var $productModel Mage_Catalog_Model_Product */
$productModel = Mage::getModel('catalog/product');

/** @var $stickerCollection Lanot_EasySticker_Model_Mysql4_Sticker_Collection */
$stickerCollection = $stickerModel->getCollection();

$this->startSetup();

//Update old attribute to prevent issues
/** @var $attributeModel Mage_Eav_Model_Entity_Attribute */
$attributeModel = Mage::getModel('eav/entity_attribute');
$attributeModel->load('lanot_sticker_id', 'attribute_code');
if ($attributeModel->getId()) {

    /** @var $productCollection Mage_Catalog_Model_Resource_Collection_Abstract */
    $productCollection = $productModel->getCollection();
    $productCollection->addAttributeToFilter('lanot_sticker_id', array('notnull' => 1));
    $stickers = $stickerCollection->load()->getColumnValues('sticker_id');


    $attributeModel->setBackendModel(null);
    $attributeModel->setSourceModel(null);
    $attributeModel->save();

} else {
    $stickers= array();
}

//tables definition
$stickerProductTable = $this->getTable('lanot_easysticker/sticker_product');


if (!empty($stickers) && ($productCollection->count() > 0)) {
    $stickersToInsert = array();

    /** @var $product Mage_Catalog_Model_Product */
    foreach($productCollection as $product) {
        if (!$product->getLanotStickerId()) {
            continue;
        }

        $productStickers = array_filter(explode(',', $product->getLanotStickerId()));
        if (empty($productStickers)) {
            continue;
        }

        foreach($productStickers as $stickerId) {
            if (in_array($stickerId, $stickers)) {
                $stickersToInsert[] = array('product_id' => $product->getId(), 'sticker_id' => $stickerId);
            }
        }

        //flush bundle
        if (!empty($stickersToInsert) && (count($stickersToInsert) >= 100)) {
            $connection->insertMultiple($stickerProductTable, $stickersToInsert);
            $stickersToInsert = array();
        }
    }

    //flush other
    if (!empty($stickersToInsert)) {
        $connection->insertMultiple($stickerProductTable, $stickersToInsert);
    }
}


$this->endSetup();