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

class Lanot_EasySticker_Model_Observer
{
    /**
     * @var Lanot_EasySticker_Model_Product_Image
     */
    protected $_imageItem = null;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * @var array
     */
    protected $_productsToStickers = array();

    /**
     * @var array
     */
    protected $_attributes = array();

    public function __construct()
    {
        $this->_attributes = $this->_getHelper()->getAllowedAttributes();
    }

    /**
     * @param Varien_Object $observer
     * @return Lanot_EasySticker_Model_Observer
     */
    public function stickerSaveAfter($observer)
    {
        /** @var Lanot_EasySticker_Model_Sticker */
        $sticker = $observer->getEvent()->getSticker();
        if ($sticker->getId()) {
            $this->_saveStickerProducts($sticker);
            $this->_getItemModel()->clearCache();
        }
        return $this;
    }

    /**
     * @param Lanot_EasySticker_Model_Sticker $sticker
     * @return Lanot_PremiumSticker_Model_Observer
     */
    protected function _saveStickerProducts(Lanot_EasySticker_Model_Sticker $sticker)
    {
        $products = $sticker->getStickerProducts();
        if (is_string($products) && !empty($products)) {
            $sticker->setProducts($this->_decode($products));
        } elseif (null !== $products) {
            $sticker->setProducts(array());
        } else {
            return $this;
        }

        if ($sticker->getId()) {
            $this->_getItemModel()->assignSelectedProducts($sticker);
        }
        return $this;
    }

    /**
     * @param Varien_Object $observer
     * @return Lanot_EasySticker_Model_Observer
     */
    public function catalogProductSaveAfter($observer)
    {
        /** @var Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getDataObject();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            return $this;
        }

        $stickers = $product->getLanotStickers();
        if (is_string($stickers) && !empty($stickers)) {
            $product->setLanotStickers($this->_decode($stickers));
        } elseif (null !== $stickers) {
            $product->setLanotStickers(array());
        } else {
            return $this;
        }

        if ($product->getId()) {
            $this->_getItemModel()->assignStickersToProduct($product);
            $this->_getItemModel()->clearCache();
        }
        return $this;
    }

    /**
     * Add Stickers To Product Model
     *
     * @param Varien_Object $observer
     * @return Lanot_EasySticker_Model_Observer
     */
    public function catalogProductLoadAfter($observer)
    {
        if (!$this->_getHelper()->isEnabled() || empty($this->_attributes)) {
            return $this;
        }
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getDataObject();
        if ($this->_canApplyModel($product)) {
            $this->prepareStickers($product);
        }
        return $this;
    }

    /**
     * Add Stickers To Product Products of Collection
     *
     * @param Varien_Object $observer
     * @return Lanot_EasySticker_Model_Observer
     */
    public function catalogProductCollectionLoadAfter($observer)
    {
        if (!$this->_getHelper()->isEnabled() || empty($this->_attributes)) {
            return $this;
        }
        /** @var Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $observer->getEvent()->getCollection();
        if ($this->_canApplyCollection($collection)) {
            $this->prepareStickers($collection);
        }
        return $this;
    }

    /**
     * @param Mage_Core_Model_Abstract|Varien_Data_Collection $products
     * @return Lanot_EasySticker_Model_Observer
     */
    public function prepareStickers($products)
    {
        if ($products instanceof Mage_Catalog_Model_Product) {
            $productIds = array($products->getId());
        } elseif ($products instanceof Varien_Data_Collection_Db) {
            $productIds = $products->getColumnValues('entity_id');
        } else {
            return array();
        }

        //load from cache stickers by products ids
        $stickers = $this->_getItemModel()->loadCacheByProductIds($productIds);
        if ($stickers === false) {//performance trick - check once
            $stickers = $this->_getStickersCollection($productIds)->getItems();
            $this->_getItemModel()->saveCacheByProductIds($stickers, $productIds);
        }

        //assign stickers to products
        if ($stickers && is_array($stickers)) {
            /** @var $s Lanot_PremiumSticker_Model_Sticker */
            foreach($stickers as $s) {
                if ($s instanceof Lanot_EasySticker_Model_Sticker) {
                    $this->addStickerToProduct($s->getProductId(), $s->getId(), $s->getData());
                }
            }
        }
        return $this;
    }

    /**
     * Merge stickers array
     *
     * @param int $productId
     * @param int $stickerId
     * @param array $data
     * @return Lanot_EasySticker_Model_Observer
     */
    public function addStickerToProduct($productId, $stickerId, $data)
    {
        $this->_productsToStickers[$productId][$stickerId] = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getProductsToStickers()
    {
        return $this->_productsToStickers;
    }

    /**
     * @param $observer
     * @return Lanot_EasySticker_Model_Observer
     */
    public function catalogProductPrepareImageBefore($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        /** @var $model Mage_Catalog_Model_Product_Image */
        $model = $observer->getEvent()->getModel();

        /** @var $helper Mage_Catalog_Helper_Image */
        $helper = $observer->getEvent()->getModel();

        $attribute = $model->getDestinationSubdir();
        $dir = $this->_getHelper()->getProductMediaPath();

        if (!$helper->getImageFile()
            && $this->_isAllowed($attribute)
            && ($product instanceof Mage_Catalog_Model_Product)
            && $product->getId()
            && !empty($this->_productsToStickers[$product->getId()])
            && $product->getData($attribute)
            && ($product->getData($attribute) != 'no_selection')
        ) {
            //fix for some customisations
            if (strpos($product->getData($attribute), '/') !== 0
                && strpos($product->getData($attribute), '\\') !== 0
            ) {
                $dir .= '/';
            }
            if ($this->_fileExists($dir . $product->getData($attribute))) {
                $productId = $product->getId();
                $newAttribute = $this->_getNewAttribute($attribute, $this->_productsToStickers[$productId], 'p_' . $productId);
                $model->setDestinationSubdir($newAttribute);
                $product->setData($newAttribute, $product->getData($attribute));
            }
        }
        return $this;
    }

    /**
     * @param $observer
     * @return Lanot_EasySticker_Model_Observer
     */
    public function catalogProductPrepareImageAfter($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        /** @var $model Mage_Catalog_Model_Product_Image */
        $model = $observer->getEvent()->getModel();
        $newAttribute = $model->getDestinationSubdir();
        @list($attribute, $hash) = explode('_lst_', $newAttribute);
        //apply stickers to resized image
        if ($attribute && $hash
            && $model->getNewFile()
            && $this->_fileExists($model->getNewFile())
            && $this->_isAllowed($attribute)
            && ($product instanceof Mage_Catalog_Model_Product)
            && !empty($this->_productsToStickers[$product->getId()])
            && $product->getData($newAttribute)
        ) {
            try {
                $this->_imageItem = $this->_getImageItem();
                $this->_imageItem->setBaseFile($model->getNewFile());

                foreach($this->_productsToStickers[$product->getId()] as $sticker) {
                    $this->_addStickerWatermarkToImage($sticker, $attribute);
                }

                //re-save already re-sized image by catalog/image helper
                $this->_imageItem->saveFile();
            } catch(Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    /**
     * @param $attribute
     * @return bool
     */
    protected function _isAllowed($attribute)
    {
        return in_array($attribute, $this->_attributes);
    }

    /**
     * @param array $productIds
     * @return Lanot_PremiumSticker_Model_Mysql4_Sticker_Collection
     */
    protected function _getStickersCollection(array $productIds)
    {
        return $this->_getItemModel()->getCollection()
            ->useActive()
            ->setCollectAll(true)
            ->useProduct($productIds);
    }

     /**
     * @return Lanot_EasySticker_Model_Product_Image
     */
    protected function _getImageItem()
    {
        return Mage::getModel('lanot_easysticker/product_image');
    }

    /**
     * @return Lanot_EasySticker_Model_Sticker
     */
    protected function _getItemModel()
    {
        return Mage::getSingleton('lanot_easysticker/sticker');
    }

    /**
     * @return Lanot_EasySticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('lanot_easysticker');
    }

    /**
     * @param $input
     * @return mixed
     */
    protected function _decode($input)
    {
        return Mage::helper('adminhtml/js')->decodeGridSerializedInput($input);
    }

    /**
     * @param $file
     * @return bool
     */
    protected function _fileExists($file)
    {
        return file_exists($file);
    }

    /**
     * @param array $sticker
     * @param $attribute
     * @param $key
     * @return Lanot_EasySticker_Model_Observer
     */
    protected function _addStickerWatermarkToImage(array $sticker, $attribute, $key = 'image')
    {
        $position = isset($sticker["position"]) ? $sticker["position"] : null;
        $scale = !empty($sticker["scale_$attribute"]) ? $sticker["scale_$attribute"] : null;
        if(!empty($sticker[$key]) && !empty($scale)) {
            $this->_imageItem->setStickerWatermark($sticker[$key], $position, null, null, null, $scale);
        }
        return $this;
    }

    /**
     * @param $attribute
     * @param array $stickers
     * @param $suffix
     * @return string
     */
    protected function _getNewAttribute($attribute, array &$stickers = array(), $suffix = '')
    {
        $str = '';
        foreach($stickers as $sticker) {
            if (isset($sticker['conditions_serialized'])) {
                unset($sticker['conditions_serialized']);
            }
            $str.= implode('_', $sticker);
        }

        $str.= $suffix;
        return $attribute . '_lst_' . $this->_getNewAttributeHash($str);
    }

    /**
     * @param $string
     * @return string
     */
    protected function _getNewAttributeHash($string)
    {
        return md5($string);
    }

    /**
     * @param Mage_Core_Model_Abstract $collection
     * @return bool
     */
    protected function _canApplyModel($model)
    {
        return ($model
            && ($model instanceof Mage_Catalog_Model_Product)
            && $model->getId()
        );
    }

    /**
     * @param Varien_Data_Collection_Db $collection
     * @return bool
     */
    protected function _canApplyCollection($collection)
    {
        return ($collection
            && ($collection instanceof Varien_Data_Collection_Db)
            && !count($collection->getSelect()->getPart(Zend_Db_Select::GROUP))
            && count($collection->getAllIds())
        );
    }
}
