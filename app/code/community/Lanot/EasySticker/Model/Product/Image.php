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
 * Private Entrepreneur Anatolii Lehkyi (aka Lanot)
 *
 * @category    Lanot
 * @package     Lanot_EasySticker
 * @copyright   Copyright (c) 2010 Anatolii Lehkyi
 * @license     http://opensource.org/licenses/osl-3.0.php
 * @link        http://www.lanot.biz/
 */
class Lanot_EasySticker_Model_Product_Image
    extends Mage_Catalog_Model_Product_Image
{
    /**
     * @var array
     */
    protected $_additionalCacheMiscParams = array();//added by Lanot

    protected $_canUseFileStorage = null;

    protected function _getFullMediaPath()
    {
        return Mage::getSingleton('lanot_easysticker/sticker')->getFullMediaPath();
    }

    /**
     * Get relative watermark file path
     * or false if file not found
     *
     * @return string | bool
     */
    protected function _getWatermarkFilePath()
    {
        $file = $this->_getFullMediaPath() . $this->getWatermarkFile();
        return file_exists($file) ? $file : false;
    }

    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setBaseFile($file)
    {
        $this->_baseFile = $file;
        $this->_newFile = $file;

        if ((!$file) || (!$this->_fileExists($file))) {
            throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
        }
        return $this;
    }

    /**
     * @fix for old magento
     *
     * First check this file on FS
     * If it doesn't exist - try to download it from DB
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename) {
        if (file_exists($filename)) {
            return true;
        } elseif($this->_canUseFileStorage()) {
            return Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function _canUseFileStorage()
    {
        if (null === $this->_canUseFileStorage) {
            $file = Mage::getBaseDir('code');
            $file.= '/core/Mage/Core/Helper/File/Storage/Database.php';
            $this->_canUseFileStorage = file_exists($file);
        }
        return $this->_canUseFileStorage;
    }


    /**
     * @return Lanot_EasySticker_Model_Image
     */
    public function getImageProcessor()
    {
        if( !$this->_processor ) {
            $this->_processor = new Lanot_EasySticker_Model_Image($this->getBaseFile());
        }
        return parent::getImageProcessor();
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $file
     * @param string $position
     * @param string $size
     * @param int $width
     * @param int $heigth
     * @param int $imageOpacity
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setStickerWatermark($file, $position=null, $size=null, $width=null, $heigth=null, $scale = null, $imageOpacity=null)
    {
        if ($this->_isBaseFilePlaceholder || !$file) {
            return $this;
        }
        $this->setWatermarkFile($file);

        if ($position)
            $this->setWatermarkPosition($position);

        if ($scale)
            $this->setWatermarkScale($scale);

        if ($size)
            $this->setWatermarkSize($size);
        if ($width)
            $this->setWatermarkWidth($width);
        if ($heigth)
            $this->setWatermarkHeigth($heigth);
        if ($imageOpacity)//@is not realized
            $this->setImageOpacity($imageOpacity);//@is not realized

        $filePath = $this->_getWatermarkFilePath();
        if($filePath) {
            $this->getImageProcessor()
                ->setWatermarkPosition( $this->getWatermarkPosition() )
                ->setWatermarkImageOpacity( $this->getWatermarkImageOpacity() )
                ->setWatermarkWidth( $this->getWatermarkWidth() )
                ->setWatermarkHeigth( $this->getWatermarkHeigth() )
                ->stickerWatermark($filePath);
        }

        return $this;
    }

    public function setWatermarkScale($scale)
    {
        $scale = (int) $scale;
        $wPath = $this->_getWatermarkFilePath();
        $iPath = $this->getBaseFile();

        if ($scale <= 0 || $scale > 100 || !$wPath || !$iPath) {
            return $this;
        }

        @list($_width, $_height) = $this->_getHelper()->getImageSize($wPath);
        if (!$_width || !$_height) {
            return $this;
        }

        try {
            $totalKoeff = $scale * $this->getImageProcessor()->getOriginalWidth() / 100;
            $localKoeff = $totalKoeff / $_width;
            $newWidth = (int) $totalKoeff;
            $newHeight = (int) ($localKoeff * $_height);

            $this->setWatermarkWidth($newWidth);
            $this->setWatermarkHeigth($newHeight);
        } catch(Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * @return Lanot_EasySticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('lanot_easysticker');
    }
}
