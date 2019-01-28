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
 * Stickers List admin grid container
 *
 * @author Lanot
 */

class Lanot_EasySticker_Block_Adminhtml_Sticker
	extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'lanot_easysticker';
        $this->_controller = 'adminhtml_sticker';
        $this->_headerText = Mage::helper('lanot_easysticker')->__('Manage Stickers');

        parent::__construct();

        if (Mage::helper('lanot_easysticker/admin')->isActionAllowed('manage_sticker/save')) {
            $this->_updateButton('add', 'label', Mage::helper('lanot_easysticker')->__('Add New Sticker'));
        } else {
            $this->_removeButton('add');
        }
    }
}

