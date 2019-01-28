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
 * Stickers list admin grid
 *
 * @author Lanot
 */
class Lanot_EasySticker_Block_Adminhtml_Sticker_Grid
    extends Lanot_EasySticker_Block_Adminhtml_Grid_Abstract
{
    protected $_gridId = 'easystickerGrid';
    protected $_entityIdField = 'sticker_id';
    protected $_itemParam = 'sticker_id';
    protected $_formFieldName = 'sticker';
    protected $_eventPrefix = 'easysticker_';

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return !$this->_getAclHelper()->isActionAllowed('manage_sticker');
    }

    /**
     * @return Lanot_EasySticker_Block_Adminhtml_Sticker_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumnAfter('position', array(
            'header' => $this->_getHelper()->__('Position'),
            'index'  => 'position',
            'type'    => 'options',
            'options' => $this->_getItemModel()->getAvailablePositions(),
            'width'   => 120,
        ), $this->_columnPrefix.'title');

        return parent::_prepareColumns();
    }
}
