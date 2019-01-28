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
 * Sticker item model
 *
 * @author Lanot
 */
class Lanot_EasySticker_Model_Sticker
    extends Mage_Core_Model_Abstract
    implements Lanot_Core_Model_Item_Interface
{
    const DEFAULT_SCALE = 35;

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const CACHE_TAG = 'COLLECTION_DATA';
    const CACHE_TAG_STICKERS = 'LANOT_EASYSTICKER_COLLECTION_DATA';

    protected $_imageDelete = null;
    protected $_eventPrefix = 'lanot_sticker';
    protected $_eventObject = 'sticker';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('lanot_easysticker/sticker');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Lanot_EasySticker_Model_Sticker
     */
    public function assignStickersToProduct($product)
    {
        $this->getResource()->assignStickersToProduct($product);
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSelectedStickersToProduct($product)
    {
        return $this->getResource()->getSelectedStickersToProduct($product);
    }

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return array(
            self::STATUS_ENABLED  => $this->_getHelper()->__('Enabled'),
            self::STATUS_DISABLED => $this->_getHelper()->__('Disabled'),
        );
    }

    /**
     * @return int
     */
    public function getStatusDisabled()
    {
        return self::STATUS_DISABLED;
    }

    /**
     * @return int
     */
    public function getStatusEnabled()
    {
        return self::STATUS_ENABLED;
    }

    /**
     * @return array
     */
    public function getAvailablePositions()
    {
        $options = array();
        foreach($this->_getPositionsModel()->toOptionArray() as $data) {
            $options[$data['value']] = $data['label'];
        }

        return $options;
    }

    /**
     * @return Mage_Adminhtml_Model_System_Config_Source_Watermark_Position
     */
    protected function _getPositionsModel()
    {
        return Mage::getSingleton('adminhtml/system_config_source_watermark_position');
    }

    /**
     * @return array
     */
    public function getAvailableScales()
    {
        $options = array('' => $this->_getHelper()->__("Don't show"));
        for($i = 5; $i<= 100; $i+=5) {
            $options[$i] = $i . '%';
        }
        return $options;
    }

    /**
     * Filesystem directory path of sticker  images
     * relatively to media folder
     *
     * @return string
     */
    public function getMediaPath()
    {
        return 'lanot/easysticker';
    }

    /**
     * Filesystem full directory path of sticker  images
     * relatively to media folder
     *
     * @return string
     */
    public function getFullMediaPath()
    {
        return Mage::getBaseDir('media') . '/' . $this->getMediaPath();
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_prepareImage();
        return $this;
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _beforeDelete()
    {
        $this->_imageDelete = $this->getImage();
        return $this;
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _afterDelete()
    {
        if ($this->_imageDelete) {
            $this->_deleteImage($this->_imageDelete);
        }
        return $this;
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _prepareImage()
    {
        $imageData = $this->getStickerImage();
        $isImageDeleted = (is_array($imageData) && !empty($imageData['delete']));

        if ($isImageDeleted) {
            $this->_deleteImage($this->getImage());
            $this->setImage(null);
        } elseif (!empty($_FILES['sticker_image']['name'])){
            if (null !== $this->getImage()) {
                $this->_deleteImage($this->getImage());
            }
            $this->setImage($this->_uploadImage('sticker_image'));
        } elseif(!$this->getImage()) {
            Mage::throwException($this->_getHelper()->__("Field 'Image' is required"));
        }
        return $this;
    }

    /**
     * @param $key
     * @return string|null
     */
    protected function _uploadImage($key)
    {
        $image = null;
        if(empty($_FILES[$key]['name'])) {
            return null;
        }

        try {
            //prepare uploader to upload
            $uploader = new Varien_File_Uploader($key);
            $uploader->setAllowedExtensions(array('png')); //'gif', 'jpg', 'jpeg'
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            // save the image to path
            $result = $uploader->save($this->getFullMediaPath());
            if (isset($result['file'])) {
                $image = $result['file'];
            }
        } catch (Exception $e) {
            Mage::logException($e);
			throw new Mage_Core_Exception($e->getMessage());
        }

        return $image;
    }

    /**
     * @param $image
     * @return bool
     */
    protected function _deleteImage($image)
    {
        $filename = $this->getFullMediaPath() . $image;
        if (file_exists($filename)) {
            $io = new Varien_Io_File();
            return $io->rm($filename);
        }
        return false;
    }

    /**
     * @return Lanot_EasySticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('lanot_easysticker');
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _afterSaveCommit()
    {
        $this->_clearCache();
        return $this;
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _afterDeleteCommit()
    {
        $this->_clearCache();
        return $this;
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _clearCache()
    {
        Mage::app()->getCacheInstance()->clean(array(self::CACHE_TAG_STICKERS));
        return $this;
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    public function clearCache()
    {
        return $this->_clearCache();
    }

    /**
     * @param $ids
     * @return string
     */
    public function getCacheKeyByProductIds($ids)
    {
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        return self::CACHE_TAG_STICKERS . '_products_'. md5(implode('_', $ids));
    }

    /**
     * @param $ids
     * @return bool|mixed
     */
    public function loadCacheByProductIds($ids)
    {
        if (empty($ids)) {
            return false;
        }
        $cacheKey = $this->getCacheKeyByProductIds($ids);
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            return unserialize($cache);
        }
        return false;
    }

    /**
     * @param $data
     * @param $ids
     * @return bool|Lanot_EasySticker_Model_Sticker
     */
    public function saveCacheByProductIds($data, $ids)
    {
        if (empty($ids)) {
            return false;
        }
        $cacheKey = $this->getCacheKeyByProductIds($ids);
        $lt = mktime(0,0,0,date('m'),date('d') + 1) - time() - 1;
        if (Mage::app()->useCache('config') && ($lt > 0)) {
            Mage::app()->saveCache(serialize($data), $cacheKey, array(self::CACHE_TAG, self::CACHE_TAG_STICKERS), $lt);
        }
        return $this;
    }
}
