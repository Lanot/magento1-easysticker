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
 * Image Renderer
 *
 * @author Lanot
 */
class Lanot_EasySticker_Block_Adminhtml_Renderer_Image extends Varien_Data_Form_Element_Image
{
    /**
     * Enter description here...
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();

        if ($this->getComment()) {
            $html .= sprintf('<div>%s</div>', $this->getComment());
        }

        return $html;
    }
}