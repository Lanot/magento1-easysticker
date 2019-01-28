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

class Lanot_EasySticker_Block_Adminhtml_Product_Edit_Tabs_Sticker_Grid
    extends Lanot_EasySticker_Block_Adminhtml_Sticker_Grid
{
    protected $_formFieldName = 'sticker';
    protected $_isTabGrid = true;
    protected $_columnPrefix = 'stickers_';
    protected $_checkboxFieldName = 'stickers_in_selected';

    //protected $_defaultLimit    = 1;

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/stickergridonly', array('_current' => true, '_secure'=>true));
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return !$this->_getAclHelper()->isActionAllowed('manage_sticker/assign');
    }

    /**
     * Retrieve selected items
     *
     * @return array
     */
    public function getSelectedLinks()
    {
        if (null !== $this->_selectedLinks) {
            return $this->_selectedLinks;
        }

        $this->_selectedLinks = array();
        $productId = $this->getRequest()->getParam('product_id');
        $product = null;
        if ($productId) {
            $product = $this->_getProductItem()->load($productId);
        }

        if ($product) {
            $this->_selectedLinks = $this->_getItemModel()->getSelectedStickersToProduct($product);
        }

        return $this->_selectedLinks;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProductItem()
    {
        return Mage::getModel('catalog/product');
    }
}
