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

class Lanot_EasySticker_Model_Product_Attribute_Source_Images
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $collection = $this->getEavImageAttributesCollection();
        /** @var $attribute Mage_Eav_Model_Entity_Attribute */
        foreach($collection as $attribute)
        {
            $options[] = array(
                'value'=> $attribute->getAttributeCode(),
                'label'=> $attribute->getFrontendLabel(),
            );
        }

        return $options;
    }

    /**
     * @param bool $allAttributes
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getEavImageAttributesCollection($allAttributes = true)
    {
        if ($allAttributes) {
            $attributes = $this->_getHelper()->getAttributes();
        } else {
            $attributes = $this->_getHelper()->getAllowedAttributes();
        }

        $collection = $this->_getEavCollection();
        $collection->addFieldToFilter('attribute_code', array('in' => $attributes));
        $collection->addFieldToFilter('entity_type_id', $this->_getEntityTypeId());

        return $collection;
    }

    /**
     * @return Lanot_EasySticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('lanot_easysticker');
    }

    /**
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected function _getEavCollection()
    {
        return Mage::getResourceModel('eav/entity_attribute_collection');
    }

    /**
     * @return int
     */
    protected function _getEntityTypeId()
    {
        return (int) Mage::getModel('eav/entity_type')->load('catalog_product', 'entity_type_code')->getId();
    }
}