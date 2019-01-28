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

class Lanot_EasySticker_Block_Adminhtml_Product_Edit_Tabs_Sticker
    extends Mage_Core_Block_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->_getHelper()->__('Associated Stickers');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Associated Stickers');
    }

    public function canShowTab()
    {
        return $this->_getAclHelper()->isActionAllowed('manage_sticker/assign');
    }

    public function isHidden()
    {
        return !$this->canShowTab();
    }

    public function getTabUrl()
    {
        return $this->getUrl('lanot_easysticker/adminhtml_sticker/stickergrid',
            array('product_id' => $this->_getProduct()->getId(), '_secure'=>true)
        );
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * @return Lanot_EasySticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('lanot_easysticker');
    }

    /**
     * @return Lanot_EasySticker_Helper_Admin
     */
    protected function _getAclHelper()
    {
        return Mage::helper('lanot_easysticker/admin');
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return MAge::registry('current_product');
    }
}
