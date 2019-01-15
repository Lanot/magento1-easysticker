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

class Lanot_EasySticker_Adminhtml_StickerController
	extends Lanot_Core_Controller_Adminhtml_AbstractController
{
    protected $_msgTitle = 'Stickers';
    protected $_msgHeader = 'Manage Stickers';
    protected $_msgItemDoesNotExist = 'The Sticker item does not exist.';
    protected $_msgItemNotFound = 'Unable to find the sticker item #%s.';
    protected $_msgItemEdit = 'Edit Sticker Item';
    protected $_msgItemNew = 'New Sticker Item';
    protected $_msgItemSaved = 'The Sticker item has been saved.';
    protected $_msgItemDeleted = 'The Sticker item has been deleted.';
    protected $_msgError = 'An error occurred while edit the Sticker item.';
    protected $_msgErrorItems = 'An error occurred while edit the Sticker items %s.';
    protected $_msgItems = 'The Sticker items (#%s) has been';

    protected $_menuActive = 'lanot/manage_sticker';
    protected $_aclSection = 'manage_sticker';

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _getItemModel()
    {
        return Mage::getModel('lanot_easysticker/sticker');
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     * @return Lanot_EasySticker_Adminhtml_StickerController
     */
    protected function _registerItem(Mage_Core_Model_Abstract $model)
    {
        Mage::register('easy.sticker.item', $model);
        return $this;
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
     * Grid with serializer ajax action
     */
    public function stickergridAction()
    {
        $this->_loadLayouts();
    }

    /**
     * Grid only ajax action
     */
    public function stickergridonlyAction()
    {
        $this->_loadLayouts();
    }

    public function previewAction()
    {
        @$image = base64_decode(urldecode($this->getRequest()->getParam('image')));
        if (empty($image)) {
            $this->getResponse()
                ->setBody('Wrong image passed to preview')
                ->sendResponse();
            exit;
        }

        $scale = $this->getRequest()->getParam('scale');
        $position = $this->getRequest()->getParam('position');
        $sample = Mage::getBaseDir('media') . DS . $this->_getHelper()->getPreviewSample();

        $model = new Lanot_EasySticker_Model_Product_Image();
        $model->setBaseFile($sample);
        $model->setStickerWatermark($image, $position, null, null, null, $scale);

        $this->getResponse()
            ->setHeader('Content-type', 'image/jpeg')
            ->setBody($model->getImageProcessor()->display())
            ->sendResponse();
            exit;
    }
}
