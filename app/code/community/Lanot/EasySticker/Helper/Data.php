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

class Lanot_EasySticker_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_NODE_CONFIG_ENABLED = 'lanot_easysticker/view/enabled';
    //const XML_NODE_CONFIG_ENABLED_PREGENERATE = 'lanot_easysticker/view/enabled_pregenerate';
    const XML_NODE_PATH_DEFAULT_ATTRIBUTES = 'default/lanot_easysticker/attributes';
    const XML_NODE_PATH_ALLOWED_ATTRIBUTES = 'lanot_easysticker/view/allowed_attributes';
    const XML_NODE_PATH_PREVIEW_SAMPLE = 'lanot_easysticker/preview/sample';
    
    /**
     * @var array
     */
    protected $_allowedAttributes = null;

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    public function getStickerItemInstance()
    {
        return Mage::registry('easy.sticker.item');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) Mage::app()->getStore()->getConfig(self::XML_NODE_CONFIG_ENABLED);
    }

    /**
     * @return bool
     */
    public function getPreviewSample()
    {
        return Mage::app()->getStore()->getConfig(self::XML_NODE_PATH_PREVIEW_SAMPLE);
    }

    /**
     * @return array
     */
    public function getAllowedAttributes()
    {
        if (null === $this->_allowedAttributes) {
            $this->_allowedAttributes = array_filter(explode(',', Mage::app()->getStore()->getConfig(
                self::XML_NODE_PATH_ALLOWED_ATTRIBUTES)));
        }
        return $this->_allowedAttributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = (array) Mage::app()->getConfig()->getNode(self::XML_NODE_PATH_DEFAULT_ATTRIBUTES)->asArray();
        if (empty($attributes)) {
            return array();
        }
        return array_keys($attributes);
    }

    /**
     * @return string
     */
    public function getProductMediaPath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }

    /**
     * @param $path
     * @return array
     */
    public function getImageSize($path)
    {
        return getimagesize($path);
    }
}
