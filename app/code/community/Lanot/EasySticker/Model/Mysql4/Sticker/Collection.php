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
 * Stickers collection
 *
 * @author Lanot
 */
class Lanot_EasySticker_Model_Mysql4_Sticker_Collection
extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_collectAll = false;

    /**
     * Define collection model
     */
    protected function _construct()
    {
        $this->_init('lanot_easysticker/sticker');
    }

    /**
     * @return string
     */
    protected function _getLinkTable()
    {
        return $this->getTable('lanot_easysticker/sticker_product');
    }

    /**
     * @param $flag
     * @return Lanot_EasySticker_Model_Mysql4_Sticker_Collection
     */
    public function setCollectAll($flag)
    {
        $this->_collectAll = $flag;
        return $this;
    }

    /**
     * Retrieve item id
     *
     * @param Varien_Object $item
     * @return mixed
     */
    protected function _getItemId(Varien_Object $item)
    {
        if ($this->_collectAll) {
            return null;
        }
        return $item->getId();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('sticker_id', 'title');
    }

    /**
     * @return Lanot_EasySticker_Model_Mysql4_Sticker_Collection
     */
    public function useActive()
    {
        $this->addFilter('main_table.is_active', Lanot_EasySticker_Model_Sticker::STATUS_ENABLED);
        return $this;
    }

    /**
     * @param $productIds
     * @return Lanot_EasySticker_Model_Mysql4_Sticker_Collection
     */
    public function useProduct($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array((int) $productIds);
        }

        $cond = 'main_table.sticker_id=ps.sticker_id AND ps.product_id IN (?)';
        $cond = $this->getConnection()->quoteInto($cond, $productIds);

        $this->getSelect()->join(array('ps' => $this->_getLinkTable()), $cond, array('product_id'));

        return $this;
    }
}
