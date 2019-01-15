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