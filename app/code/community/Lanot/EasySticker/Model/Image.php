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
 * Image  Model (Processor)
 *
 * @author Lanot
 */
class Lanot_EasySticker_Model_Image
    extends Varien_Image
{
    /**
     * Constructor
     *
     * @param Varien_Image_Adapter $adapter. Default value is GD2
     * @param string $fileName
     * @return void
     */
    public function __construct($fileName=null, $adapter=Varien_Image_Adapter::ADAPTER_GD2)
    {
        $this->_getAdapter($adapter);
        $this->_fileName = $fileName;
        if( isset($fileName) ) {
            $this->open();
        }
    }

    /**
     * Retrieve image adapter object
     *
     * @param string $adapter
     * @return Varien_Image_Adapter_Abstract
     */
    protected function _getAdapter($adapter=null)
    {
        if(!isset($this->_adapter)) {
            $this->_adapter = new Lanot_EasySticker_Model_Image_Adapter_Gd2();
        }
        return $this->_adapter;
    }


    /**
     * Adds watermark to our image.
     *
     * @param string $watermarkImage. Absolute path to watermark image.
     * @param int $positionX. Watermark X position.
     * @param int $positionY. Watermark Y position.
     * @param int $watermarkImageOpacity. Watermark image opacity.
     * @param bool $repeat. Enable or disable watermark brick.
     * @access public
     * @return void
     */
    public function stickerWatermark($watermarkImage, $positionX=0, $positionY=0, $watermarkImageOpacity=30, $repeat=false)
    {
        if( !file_exists($watermarkImage) ) {
            throw new Exception("Required file '{$watermarkImage}' does not exists.");
        }

        if ($this->_getAdapter() instanceof Lanot_EasySticker_Model_Image_Adapter_Gd2) {
            $this->_getAdapter()->stickerWatermark($watermarkImage, $positionX, $positionY, /* $watermarkImageOpacity, */ $repeat);
        } else {
            $this->_getAdapter()->watermark($watermarkImage, $positionX, $positionY, $watermarkImageOpacity, $repeat);
        }
    }
}
