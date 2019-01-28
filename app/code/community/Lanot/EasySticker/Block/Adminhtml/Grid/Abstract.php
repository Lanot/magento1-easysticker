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

/**
 * stickers list admin grid abstract
 *
 * @author Lanot
 */
class Lanot_EasySticker_Block_Adminhtml_Grid_Abstract
    extends Lanot_Core_Block_Adminhtml_Grid_Abstract
{
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
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _getItemModel()
    {
        return Mage::getSingleton('lanot_easysticker/sticker');
    }
}
