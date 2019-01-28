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

class Lanot_EasySticker_Model_Product_Attribute_Backend_Sticker
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before Attribute Save Process
     *
     * @param Mage_Catalog_Model_Product $object
     * @return Lanot_EasySticker_Model_Product_Attribute_Backend_Sticker
     */
    public function beforeSave($object) {
        $attributeCode = $this->getAttribute()->getName();
        $data = $object->getData($attributeCode);

        if (!is_array($data)) {
            $data = array();
        }

        $object->setData($attributeCode, implode(',', $data));

        if (is_null($object->getData($attributeCode))) {
            $object->setData($attributeCode, null);
        }

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return Lanot_EasySticker_Model_Product_Attribute_Backend_Sticker
     */
    public function afterLoad($object) {
        $attributeCode = $this->getAttribute()->getName();
        $data = $object->getData($attributeCode);
        if ($data) {
            $object->setData($attributeCode, explode(',', $data));
        }

        return $this;
    }
}
