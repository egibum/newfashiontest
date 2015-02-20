<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_FeedSalidzini
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

class ET_FeedSalidzini_Model_Feedsalidzini extends Mage_Core_Model_Abstract
{
    /**
     * Real file path
     *
     * @var string
     */
    protected $_filePath;

    protected $_filterStock = false;
    protected $_filterStockFrom = 0;
    protected $_filterPrice = 0;

    protected $_filterProductVisibility = false;
    protected $_filterProductTypes = array();

    protected $_hideDuplicates = true;

    protected $_attributeSymbol = '@';
    protected $_nameFormat = '@name';
    protected $_nameAttributes = array('manufacturer');
    protected $_attributeOptions = array();

    protected $_includeStock = true;
    protected $_hideLargeStock = false;
    protected $_hideAllStock = false;
    protected $_emulateStock = false;
    protected $_emulateStockDefault = false;

    protected $_includeDeliveryCost = false;
    protected $_deliveryDefaultValue = 0;
    protected $_deliveryFreeFromPrice = false;

    protected $_tmpCategories = array();

    protected $_stock = array();
    protected $_superProducts = array();

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('feedsalidzini/filelist');

        $this->_filterStock = Mage::getStoreConfig('feedsalidzini/filter/filter_stock');
        $this->_filterStockFrom = Mage::getStoreConfig('feedsalidzini/filter/filter_stock_from');
        $this->_filterPrice = Mage::getStoreConfig('feedsalidzini/filter/filter_price');

        $this->_hideDuplicates = Mage::getStoreConfig('feedsalidzini/filter/filter_duplicates');

        if (Mage::getStoreConfig('feedsalidzini/general/mapping_name_attribute')) {
            $this->_nameFormat = Mage::getStoreConfig('feedsalidzini/general/mapping_name_attribute');
        }
        $tmp = array();
        preg_match_all('/' . $this->_attributeSymbol . '([A-Za-z0-9_]+)/', $this->_nameFormat, $tmp);
        if (isset($tmp[1])) {
            $this->_nameAttributes = array_merge($this->_nameAttributes, $tmp[1]);
        }
        usort($this->_nameAttributes, array($this, "_sortAttributes"));

        $this->_filterProductVisibility = explode(
            ",",
            Mage::getStoreConfig('feedsalidzini/filter/filter_product_visibility')
        );
        $this->_filterProductTypes = explode(",", Mage::getStoreConfig('feedsalidzini/filter/filter_product_type'));

        $this->_hideLargeStock = Mage::getStoreConfig('feedsalidzini/general/hide_large_stock');
        if ($this->_hideLargeStock) {
            $this->_hideLargeStock = Mage::getStoreConfig('feedsalidzini/general/hide_large_stock_value');
        }

        $this->_hideAllStock = Mage::getStoreConfig('feedsalidzini/general/hide_all_stock');
        if ($this->_hideAllStock) {
            $this->_hideAllStock = Mage::getStoreConfig('feedsalidzini/general/hide_all_stock_value');
        }

        $this->_includeStock = Mage::getStoreConfig("feedsalidzini/general/include_stock_value");
        $this->_emulateStock = Mage::getStoreConfig("feedsalidzini/general/emulate_stock_for_containers");
        if ($this->_emulateStock && Mage::getStoreConfig("feedsalidzini/general/emulate_stock_from_subproducts")) {
            $this->_emulateStock = 2;
        }

        if ($this->_emulateStock == 1) {
            $this->_emulateStockDefault = Mage::getStoreConfig("feedsalidzini/general/emulate_stock_default");
        }

        $this->_includeDeliveryCost = Mage::getStoreConfig('feedsalidzini/general/delivery_include');
        $this->_deliveryDefaultValue = Mage::getStoreConfig('feedsalidzini/general/delivery_default_value');
        $this->_deliveryFreeFromPrice = Mage::getStoreConfig('feedsalidzini/general/delivery_free_from_price');
    }


    protected function _beforeSave()
    {
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . '/' . $this->getXmlfilePath());

        /**
         * Check path is allow
         */
        if (!$io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException(Mage::helper('feedsalidzini')->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$io->fileExists($realPath, false)) {
            Mage::throwException(
                Mage::helper('feedsalidzini')->__(
                    'Please create the specified folder "%s" before saving the xml.',
                    Mage::helper('core')->htmlEscape($this->getXmlfilePath())
                )
            );
        }

        if (!$io->isWriteable($realPath)) {
            Mage::throwException(
                Mage::helper('feedsalidzini')->__(
                    'Please make sure that "%s" is writable by web-server.',
                    $this->getXmlfilePath()
                )
            );
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getXmlfileFilename())) {
            Mage::throwException(
                Mage::helper('feedsalidzini')->__(
                    'Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the'
                        . ' filename. No spaces or other characters are allowed.'
                )
            );
        }
        if (!preg_match('#\.xml$#', $this->getXmlfileFilename())) {
            $this->setXmlfileFilename($this->getXmlfileFilename() . '.xml');
        }

        $this->setXmlfilePath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/');

        return parent::_beforeSave();
    }

    /**
     * Return real file path
     *
     * @return string
     */
    protected function getPath()
    {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace(
                '//',
                '/',
                Mage::getBaseDir() . $this->getXmlfilePath()
            );
        }
        return $this->_filePath;
    }

    /**
     * Return full file name with path
     *
     * @return string
     */
    public function getPreparedFilename()
    {
        return $this->getPath() . $this->getXmlfileFilename();
    }

    /**
     * Generate XML file
     *
     * @return ET_FeedSalidzini_Model_Feedsalidzini
     */
    public function generateXml()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        if ($io->fileExists($this->getXmlfileFilename()) && !$io->isWriteable($this->getXmlfileFilename())) {
            Mage::throwException(
                Mage::helper('feedsalidzini')->__(
                    'File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.',
                    $this->getXmlfileFilename(), $this->getPath()
                )
            );
        }


        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><root></root>');
        $storeId = $this->getStoreId();

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection
         * @var $product Mage_Catalog_Model_Product
         * @var $categories Mage_Catalog_Model_Resource_Category_Collection
         * @var $category Mage_Catalog_Model_Category
         * @var $stockCollection Mage_CatalogInventory_Model_Resource_Stock_Item_Collection
         * @var $stockItem Mage_CatalogInventory_Model_Stock_Item
         */

        /* Collecting categories info */
        $categories = Mage::getModel('catalog/category')->getCollection();
        $categories->addIsActiveFilter();
        $categories->addNameToResult();
        $categories->addUrlRewriteToResult();
        foreach ($categories as $category) {
            $this->_tmpCategories[$category->getId()] = array(
                'id' => $category->getId(),
                'level' => $category->getLevel(),
                'name' => $category->getName(),
                'path' => $category->getPathIds(),
                //'url' => str_replace($_SERVER["SCRIPT_FILENAME"] . "/", "", $category->getUrl()),
                'url' => ($_SERVER["SCRIPT_FILENAME"]) ? str_replace($_SERVER["SCRIPT_FILENAME"] . "/", "", $category->getUrl()) : $category->getUrl(),
                'parent' => $category->getParentCategory()->getId()
            );
        }

        /* Collecting product stock info */
        $stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection();
        foreach ($stockCollection as $stockItem) {
            $this->_stock[$stockItem->getProductId()] = $stockItem->getQty();
        }
        unset($stockCollection);

        /* Subproduct collection */
        if ($this->_includeStock && $this->_emulateStock == 2) {
            /* @var $db Varien_Db_Adapter_Interface */
            $db = Mage::getSingleton('core/resource')->getConnection('core_write');
            $model = Mage::getResourceModel('catalog/product');
            $superTable = $model->getTable("catalog_product_super_link");
            $query = $db->query("SELECT * FROM " . $superTable);
            while ($row = $query->fetch()) {
                if (!isset($this->_superProducts[$row["parent_id"]])) {
                    $this->_superProducts[$row["parent_id"]] = array();
                }
                $this->_superProducts[$row["parent_id"]][] = $row["product_id"];
            }
        }

        /* Attribute collection */
        /* @var $attributes Mage_Eav_Model_Resource_Entity_Attribute_Collection
         * @var $attribute Mage_Eav_Model_Entity_Attribute
         */
        $attributes = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
            ->getAttributeCollection()
            ->addFieldToFilter('attribute_code', array('in', $this->_nameAttributes));
        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getFrontendInput(), array('select', 'multiselect'))) {
                continue;
            }

            $options = $attribute->getSource()->getAllOptions();
            $this->_attributeOptions[$attribute->getAttributeCode()] = array();
            foreach ($options as $option) {
                $this->_attributeOptions[$attribute->getAttributeCode()][$option['value']] = $option['label'];
            }
        }

        /* Collecting product info */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addFieldToFilter("type_id", array("in" => $this->_filterProductTypes));
        $collection->addAttributeToFilter("visibility", array("in" => $this->_filterProductVisibility));
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToSelect("name");
        foreach ($this->_nameAttributes as $attr) {
            $collection->addAttributeToSelect($attr);
        }
        if (!in_array("manufacturer", $this->_nameAttributes)) {
            $collection->addAttributeToSelect("manufacturer");
        }
        $collection->addAttributeToSelect("model");
        $collection->addAttributeToSelect("price");
        $collection->addAttributeToSelect("status");
        $collection->addCategoryIds();
        $collection->addUrlRewrite();
        $collection->addFinalPrice();
        //echo $collection->getSelect();
        foreach ($collection as $product) {
            if (!$this->_shouldExport($product)) {
                continue;
            }

            $maxLevel = -1;
            if ($this->_hideDuplicates) {
                $selectedCategory = false;
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (isset($this->_tmpCategories[$categoryId])) {
                        $categoryInfo = $this->_tmpCategories[$categoryId];
                        if ($categoryInfo['level'] > $maxLevel) {
                            $maxLevel = $categoryInfo['level'];
                            $selectedCategory = $categoryInfo;
                        }
                    }
                }

                $xml = $this->_addItemXml($xml, $product, $selectedCategory, $this->_stock);
            } else {
                foreach ($product->getCategoryIds() as $categoryId) {
                    if (isset($this->_tmpCategories[$categoryId]) && $this->_tmpCategories[$categoryId]['level'] > 1) {
                        $selectedCategory = $this->_tmpCategories[$categoryId];
                        $xml = $this->_addItemXml($xml, $product, $selectedCategory);
                    }
                }
            }
        }
        unset($collection);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        $io->streamOpen($this->getXmlfileFilename());
        $io->streamWrite($xml->asXML());
        $io->streamClose();

        $this->setXmlfileTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }

    protected function _getStock(Mage_Catalog_Model_Product $product, $stock)
    {
        if ($this->_isComplexProduct($product)) {
            $stock = 0;
            switch ($this->_emulateStock) {
                case 2:
                    if (isset($this->_stock[$product->getId()])) {
                        foreach ($this->_superProducts[$product->getId()] as $subProduct) {
                            $stock += (float)$this->_stock[$subProduct];
                        }
                    }
                    break;
                case 1:
                    $stock = (int)$this->_emulateStockDefault;
                    break;
                case 0:
                default:
                    break;
            }
        } elseif (!$product->getStockItem()->getIsInStock()) {
            return 0;
        }

        if ($this->_hideLargeStock && $stock > $this->_hideLargeStock) {
            $stock = $this->_hideLargeStock;
        }

        if ($this->_hideAllStock) {
            $stock = $this->_hideAllStock;
        }

        return floor($stock);
    }

    protected function _getDeliveryCost(Mage_Catalog_Model_Product $product)
    {
        $cost = 0;
        if ($this->_deliveryDefaultValue) {
            $cost = $this->_deliveryDefaultValue;
        }

        if ($this->_deliveryFreeFromPrice && $product->getPrice() > $this->_deliveryFreeFromPrice) {
            $cost = 0;
        }

        return $cost;
    }

    protected function _shouldExport(Mage_Catalog_Model_Product $product)
    {
        if ($this->_filterPrice && $product->getFinalPrice() < $this->_filterPrice) {
            return false;
        }

        if ($this->_isComplexProduct($product)) {
            return true;
        }

        if (!$this->_filterStock && !$product->getStockItem()->getIsInStock()) {
            return false;
        }

        if (!$this->_filterStock
            && $this->_filterStockFrom
            && $this->_stock[$product->getId()]
                < $this->_filterStockFrom
        ) {
            return false;
        }

        return true;
    }

    protected function _addItemXml($xml, Mage_Catalog_Model_Product $product, $selectedCategory)
    {
        $item = $xml->addChild("item");

        $name = $this->_nameFormat;
        foreach ($this->_nameAttributes as $attr) {
            $attrData = $product->getData($attr);
            if (isset($this->_attributeOptions[$attr]) && isset($this->_attributeOptions[$attr][$attrData])) {
                $attrData = $this->_attributeOptions[$attr][$attrData];
            }
            $name = str_replace($this->_attributeSymbol . $attr, $attrData, $name);
        }
        $item->name = $name;

        // Generating full URL for a product
        $categorySuffix = Mage::getStoreConfig("catalog/seo/category_url_suffix");
        if ($categorySuffix && strstr($selectedCategory['url'], $categorySuffix)) {
            $item->link = str_replace($categorySuffix, "/" . $product->getRequestPath(), $selectedCategory['url']);
        } else {
            $item->link = $selectedCategory['url'] . "/" . $product->getRequestPath();
        }

        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper = Mage::helper('tax');
        $priceIncludingTax = $taxHelper->getPrice($product, $product->getFinalPrice(1), true);
        $item->price = $priceIncludingTax;
        //$item->price = number_format((float)$product->getFinalPrice(1), 2);
        $imageUrl = Mage::getUrl('feedsalidzini/info/image/', array('product' => $product->getId()));
        $item->image = ($_SERVER["SCRIPT_FILENAME"]) ? str_replace(
            $_SERVER["SCRIPT_FILENAME"] . "/",
            "",
            $imageUrl
        ) : $imageUrl;
        $categoryName = $selectedCategory['name'];
        $parentId = $selectedCategory['parent'];
        while ($parentId) {
            if ($this->_tmpCategories[$parentId]['level'] > 1) {
                $categoryName = $this->_tmpCategories[$parentId]['name'] . " >> " . $categoryName;
                $parentId = $this->_tmpCategories[$parentId]['parent'];
            } else {
                break;
            }
        }
        $item->category_full = $categoryName;
        $item->category_link = $selectedCategory['url'];
        $item->manufacturer = $this->_attributeOptions['manufacturer'][$product->getManufacturer()];

        if ($product->getModel()) {
            $item->model = $product->getModel();
        }

        if ($this->_includeStock && (!$this->_isComplexProduct($product) || $this->_emulateStock)) {
            $item->in_stock = $this->_getStock($product, $this->_stock[$product->getId()]);
        }

        if ($this->_includeDeliveryCost) {
            $item->delivery_cost_riga = $this->_getDeliveryCost($product);
        }
        return $xml;
    }

    protected function _isComplexProduct(Mage_Catalog_Model_Product $product)
    {
        return (in_array($product->getTypeId(), array("configurable", "bundle", "grouped")));
    }

    protected function _sortAttributes($a, $b)
    {
        if (strlen($a) == strlen($b)) {
            return 0;
        }

        return (strlen($a) > strlen($b)) ? -1 : 1;
    }
}
