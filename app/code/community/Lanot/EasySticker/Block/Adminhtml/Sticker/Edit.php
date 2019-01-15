<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Lanot
 * @package     Lanot_EasySticker
 * @copyright   Copyright (c) 2012 Lanot
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sticker Sticker admin edit form container
 *
 * @author Lanot
 */
class Lanot_EasySticker_Block_Adminhtml_Sticker_Edit
	extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     *
     */
    public function __construct()
    {
        $this->_objectId   = 'id';        
        $this->_blockGroup = 'lanot_easysticker';
        $this->_controller = 'adminhtml_sticker';

        parent::__construct();

        //check permissions
        if (Mage::helper('lanot_easysticker/admin')->isActionAllowed('manage_sticker/save')) {
            $this->_updateButton('save', 'label', Mage::helper('lanot_easysticker')->__('Save Sticker Item'));
            $this->_addButton('saveandcontinue', array(
                'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ), -100);

            $this->_formScripts[] = "
            	function saveAndContinueEdit(){
            		editForm.submit($('edit_form').action+'back/edit/');
            	}";
        } else {
            $this->_removeButton('save');
        }

        if (Mage::helper('lanot_easysticker/admin')->isActionAllowed('manage_sticker/delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('lanot_easysticker')->__('Delete Sticker Item'));
        } else {
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
    	$header = Mage::helper('lanot_easysticker')->__('New Sticker Item');
        $model = Mage::helper('lanot_easysticker')->getStickerItemInstance();
        
        if ($model->getId()) {
        	$title = $this->escapeHtml($model->getTitle());
            $header = Mage::helper('lanot_easysticker')->__("Edit Sticker Item '%s'", $title);
        }        
        return $header;
    }
}
