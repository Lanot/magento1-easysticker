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
 * Sticker admin edit form main tab block
 *
 * @author Lanot
 */
class Lanot_EasySticker_Block_Adminhtml_Sticker_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_idPrefix = 'sticker_main_';

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
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected function _getAttributesCollection()
    {
        return Mage::getModel('lanot_easysticker/product_attribute_source_images')->getEavImageAttributesCollection();
    }

    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
    	/* @var $model Lanot_EasySticker_Model_Sticker */
        $model = $this->_getHelper()->getStickerItemInstance();

        /* @var $helper Lanot_EasySticker_Helper_Admin */
        $helper = $this->_getAclHelper();

        /**
         * Checking if user have permissions to save information
         */
        if ($helper->isActionAllowed('manage_sticker/save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('sticker_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->_getHelper()->__('Sticker Item Info')
        ));

        $this->_addElementTypes($fieldset);

        if ($model->getId()) {
            $fieldset->addField('sticker_id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'     => 'title',
            'label'    => $this->_getHelper()->__('Title'),
            'title'    => $this->_getHelper()->__('Title'),
            'required' => true,
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('is_active', 'select', array(
            'name'     => 'is_active',
            'label'    => $this->_getHelper()->__('Is Active'),
            'title'    => $this->_getHelper()->__('Is Sticker Active?'),
            'required' => true,
            'options'  => $model->getAvailableStatuses(),
            'style'    => 'width: 200px',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('image', 'image', array(
            'name'      => 'sticker_image',
            'label'    => $this->_getHelper()->__('Image (*.png)'),
            'comment'   => $this->_getHelper()->__('Upload image with transparent background'),
            'required' => true,
            'disabled' => $isElementDisabled,
            'style'    => 'width: 200px;',
        ));

        $fieldset->addField('position', 'select', array(
            'name'     => 'position',
            'label'    => $this->_getHelper()->__('Position'),
            'title'    => $this->_getHelper()->__('Watermark position on image'),
            'required' => true,
            'options'  => $model->getAvailablePositions(),
            'disabled' => $isElementDisabled,
            'style'    => 'width: 200px;',
        ));

        //Scales for image attributes
        foreach($this->_getAttributesCollection() as $attribute) {
            //$comment = 'Watermark size (Ex. 20% to Original Image)';
            $fieldset->addField('scale_' . $attribute->getAttributeCode(), 'select', array(
                'name'     => 'scale_' . $attribute->getAttributeCode(),
                'label'    => $this->_getHelper()->__('Size for "%s"', $attribute->getFrontendLabel()),
                'title'    => $this->_getHelper()->__('Size for "%s"', $attribute->getFrontendLabel()),
                'options'  => $model->getAvailableScales(),
                'disabled' => $isElementDisabled,
                'style'    => 'width: 50px;',
            ));
        }

        $form->setValues($model->getData());

        Mage::dispatchEvent('adminhtml_easysticker_edit_tab_main_prepare_form', array(
            'form'    => $form,
            'sticker' => $model
        ));

        $this->setForm($form);
        $this->_preparePreview();
        $this->_prepareImage();

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return $this->_getHelper()->__('Sticker Info');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Sticker Info');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * @return Lanot_EasySticker_Block_Adminhtml_Sticker_Edit_Tab_Main
     */
    protected function _prepareImage()
    {
        /** @var $element Varien_Data_Form_Element_Image */
        $element = $this->getForm()->getElement('image');
        if ($element->getValue()) {
            $path = $this->_getHelper()->getStickerItemInstance()->getMediaPath()
                . $element->getValue();
            $element->setValue($path);
        }
        return $this;
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('lanot_easysticker/adminhtml_renderer_image')
        );
    }

    /**
     * @return Lanot_EasySticker_Block_Adminhtml_Sticker_Edit_Tab_Main
     */
    protected function _preparePreview()
    {
        $image = null;
        $position = $this->getForm()->getElement('position')->getValue();
        $imageElement = $this->getForm()->getElement('image');
        if ($imageElement && $imageElement->getValue()) {
            $image = $imageElement->getValue();
        }

        foreach($this->_getAttributesCollection() as $attribute) {
            $element = $this->getForm()->getElement('scale_' . $attribute->getAttributeCode());
            $html = '&nbsp;&nbsp;' . $this->_getPreviewHtml($image, $element->getValue(), $position);
            $element->setData('after_element_html', $html);
        }

        return $this;
    }

    /**
     * @param $image
     * @param $scale
     * @param $position
     * @return string
     */
    protected function _getPreviewHtml($image, $scale, $position)
    {
        if (!$image || !$scale) {
            return $this->_getHelper()->__("Preview available after save");
        } else {
            $url = $this->getUrl('*/*/preview', array(
                'image'     => urlencode(base64_encode($image)),
                'scale'      => $scale,
                'position'  => $position,
            ));

            return sprintf(
                '<a href="%s" class="sticker-preview" rel="lightbox" target="_blank" title="%s">%s</a>',
                $url,
                $this->_getHelper()->__("Size %d%%. Position: %s", $scale, $position),
                $this->_getHelper()->__("Preview")
            );
        }
    }
}
