<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Model_Request
    extends Mage_Core_Model_Abstract
{
    protected $_enabledFilters = array();
    protected $_inPathEnabled  = false;

    /** @var \Mage_Core_Controller_Request_Http  */
    protected $_request;
    protected $_filterAliasByKey;
    protected $_filterAliasById;
    protected $_categories;
    protected $_baseUrl;

    protected $_clearParams = array('p', '___SID', 'cat');
    protected $_queryParams;

    protected $_filterableAttributes;
    protected $_filterableDecimalAttributes;

    public function __construct()
    {
        $this->_request = Mage::app()->getFrontController()->getRequest();
        $this->_initAliases();
        $this->_initializeBaseUrl($this->_request);
        $this->_initializeRequest($this->_request);
    }


    /**
     * @return array
     */
    public function getQueryParams()
    {
        if (is_null($this->_queryParams)) {
            $query = $this->_request->getQuery();
            foreach ($this->_clearParams as $param) {
                if (array_key_exists($param, $query)) {
                    unset($query[$param]);
                }
            }
            foreach ($this->getFilterableAttributes() as $attribute) {
                if (isset($query[$attribute->getAttributeCode()])) {
                    unset($query[$attribute->getAttributeCode()]);
                }
            }

            $this->_queryParams = $query;
        }

        return $this->_queryParams;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function getParam($key, $default = null)
    {
        return $this->_request->getParam($key, $default);
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return $this
     */
    protected function _initializeBaseUrl(Mage_Core_Controller_Request_Http $request)
    {
        if ($url = $request->getParam('layered_navigation_base_url')) {
            $this->_baseUrl = $url;
        } else {
            $this->_baseUrl = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_nosid' => true));
        }

        $this->_baseUrl = Mage::helper('ecommerceteam_sln/request')->clearSuffix($this->_baseUrl);

        return $this;
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return $this
     */
    protected function _initializeRequest(Mage_Core_Controller_Request_Http $request)
    {
        $filters = array();
        if ($layeredPathInfo = $request->getParam('layered_navigation_path')) {
            $this->_inPathEnabled = true;
            $filters = $this->_extractAttributeFilterInformation($layeredPathInfo);
            $categories = $this->_extractCategoryFilterInformation($layeredPathInfo);
            $filters = array_merge($filters, $categories);
            $anonymousAttributes = $this->_extractAnonymousFilterInformation($layeredPathInfo);
            foreach ($anonymousAttributes as $key) {
                $filterData = $this->getFilterIdByKey(null, $key);
                if (is_array($filterData)) {
                    $attribute = $this->getAttributeByCode($filterData[0]);
                    if ($attribute) {
                        $attributeCode = $attribute->getAttributeCode();
                        $this->_enabledFilters[$attributeCode][] = $filterData[1];
                    }
                } else if (is_numeric($key)) {
                    $attribute = $this->getAttributeByOption($key);
                    if ($attribute) {
                        $attributeCode = $attribute->getAttributeCode();
                        $this->_enabledFilters[$attributeCode][] = $key;
                    }
                }
            }
        }
        if ($categoryFilter = $this->getParam('cat')) {
            if (is_array($categoryFilter)) {
                $categories = array('cat' => $categoryFilter);
            } else {
                $categories = $this->_extractCategoryFilterInformation($categoryFilter);
            }
            if(!empty($categories)) {
                if (isset($filters['cat'])) {
                    $filters['cat'] = array_merge($filters['cat'], $categories['cat']);
                } else {
                    $filters['cat'] = $categories['cat'];
                }
            }
        }

        // Initialize attributes
        foreach ($this->getFilterableAttributes() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (isset($filters[$attributeCode])) {
                $this->_enabledFilters[$attributeCode] = $filters[$attributeCode];
            } else if ($value = $this->_request->getParam($attributeCode)) {
                if (is_array($value)) {
                    if ('decimal' == $attribute->getBackendType()) {
                        $this->_enabledFilters[$attributeCode] = $value;
                    } else {
                        foreach ($value as $_value) {
                            if ($optionId = $this->getFilterIdByKey($attributeCode, $_value)) {
                                $this->_enabledFilters[$attributeCode][] = $optionId;
                            } else if (is_numeric($_value)) {
                                $this->_enabledFilters[$attributeCode][] = $_value;
                            }
                        }
                    }
                } else {
                    if ('decimal' == $attribute->getBackendType()) {
                        $matches = array();
                        if (preg_match_all('/\d+\\-\d+/', $value, $matches)) {
                            $this->_enabledFilters[$attributeCode] = $matches[0];
                        }
                    } else {
                        foreach (explode('-', $value) as $_value) {
                            if ($optionId = $this->getFilterIdByKey($attributeCode, $_value)) {
                                $this->_enabledFilters[$attributeCode][] = $optionId;
                            } else if (is_numeric($_value)) {
                                $this->_enabledFilters[$attributeCode][] = $_value;
                            }
                        }
                    }
                }
            }
        }

        // Initialize categories
        if (isset($filters['cat']) && !empty($filters['cat'])) {
            foreach ($filters['cat'] as $value) {
                if ($_value = $this->getFilterIdByKey('cat', $value)) {
                    $this->_enabledFilters['cat'][] = $_value;
                } else if (is_numeric($value)) {
                    $this->_enabledFilters['cat'][] = $_value;
                }
            }
        }

        return $this;
    }

    /**
     * @param string $requestStr
     * @return array
     */
    protected function _extractAttributeFilterInformation(&$requestStr)
    {
        $result = array();

        foreach ($this->getFilterableAttributes() as $attribute) {
            if ('decimal' == $attribute->getBackendType()) {
                $attributeCode = $attribute->getAttributeCode();
                if (preg_match_all('/('.$attributeCode.')\\-(\d+\\-\d+)/', $requestStr, $matches)) {
                    foreach ($matches[0] as $i => $match) {
                        $result[$attributeCode][] = $matches[2][$i];
                    }
                    // Clear prices from request string
                    $requestStr = preg_replace('/('.$attributeCode.')\\-(\d+\\-\d+|\d+)/', '', $requestStr);
                    $requestStr = preg_replace('/\\-+/', '-', $requestStr);
                }
            }
        }

        return $result;
    }

    /**
     * @param $requestStr
     * @return array
     */
    protected function _extractCategoryFilterInformation(&$requestStr)
    {
        $result = array();
        foreach ($this->_getCategories() as $category) {
            $matches = array();
            $key = addcslashes($category->getData('url_key'), '/');
            if (preg_match('/\b'.$key.'\b/ui', $requestStr, $matches)) {
                $result['cat'][] = $category->getData('url_key');
                $requestStr = preg_replace('/\b'.$key.'\b/ui', '', $requestStr);
                $requestStr = preg_replace('/\\-+/', '-', $requestStr);
            }
        }

        return $result;
    }

    /**
     * @param string $requestStr
     * @return array
     */
    protected function _extractAnonymousFilterInformation(&$requestStr)
    {
        if (strlen($requestStr)) {
            return explode('-', trim($requestStr, '/'));
        }

        return array();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * @return EcommerceTeam_Sln_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('ecommerceteam_sln');
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        if (is_null($this->_filterableAttributes)) {
            if ($this->getHelper()->isSearch()){
                /** @var $layer Mage_CatalogSearch_Model_Layer */
                $layer =  Mage::getSingleton('catalogsearch/layer');
                $this->_filterableAttributes = $layer->getFilterableAttributes();
            } else {
                /** @var $layer Mage_Catalog_Model_Layer */
                $layer =  Mage::getSingleton('catalog/layer');
                $this->_filterableAttributes = $layer->getFilterableAttributes();
            }
        }

        return $this->_filterableAttributes;
    }

    /**
     * @return Varien_Data_Collection
     */
    public function getFilterableDecimalAttributes()
    {
        if (is_null($this->_filterableDecimalAttributes)) {
            $this->_filterableDecimalAttributes = new Varien_Data_Collection();
            foreach ($this->getFilterableAttributes() as $attribute) {
                if ('decimal' == $attribute->getBackendType()) {
                    $this->_filterableDecimalAttributes->addItem($attribute);
                }
            }
        }

        return $this->_filterableDecimalAttributes;
    }

    /**
     * @param $attributeCode
     * @return bool|Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeByCode($attributeCode)
    {
        $attributes = $this->getFilterableAttributes();
        if ($attributes instanceof Varien_Data_Collection) {
            if ($attribute = $attributes->getItemByColumnValue('attribute_code', $attributeCode)) {
                return $attribute;
            }
        }

        return false;
    }

    /**
     * @param $optionId
     * @return bool|Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeByOption($optionId)
    {
        $attributes = $this->getFilterableAttributes();
        foreach ($attributes as $attribute) { /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            if ($attribute->usesSource()) {
                if ($attribute->getSource()->getOptionText($optionId)) {
                    return $attribute;
                }
            }
        }

        return false;
    }


    /**
     * @param string $requestVar
     * @param string $key
     * @return null|int
     */
    public function getFilterIdByKey($requestVar = null, $key)
    {
        if (is_null($requestVar)) {
            foreach ($this->_filterAliasByKey as $requestVar => $filterData) {
                if (isset($filterData[$key])) {
                    return array($requestVar, $filterData[$key]);
                }
            }
        } else {
            if (isset($this->_filterAliasByKey[$requestVar][$key])){
                return $this->_filterAliasByKey[$requestVar][$key];
            }
        }

        return null;
    }

    /**
     * @param string $requestVar
     * @param int $id
     * @return null|string
     */
    public function getFilterKeyById($requestVar = null, $id)
    {
        if (is_null($requestVar)) {
            foreach ($this->_filterAliasById as $requestVar => $filterData) {
                if (isset($filterData[$id])) {
                    return array($requestVar, $filterData[$id]);
                }
            }
        } else {
            if (isset($this->_filterAliasById[$requestVar][$id])){
                return $this->_filterAliasById[$requestVar][$id];
            }
        }

        return null;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection|array
     */
    protected function _getCategories()
    {
        if (is_null($this->_categories)) {
            $this->_categories = Mage::helper('ecommerceteam_sln/request')->getCategories();
            if (!is_array($this->_categories) && !$this->_categories instanceof Varien_Data_Collection) {
                $this->_categories = array();
            }
        }

        return $this->_categories;
    }

    /**
     * Init attribute aliases
     *
     * @return $this
     */
    protected function _initAliases()
    {
        $aliasCollection = Mage::getResourceModel('ecommerceteam_sln/attribute_collection');
        $aliasCollection->getSelect()->join(
                    array('attribute' => $aliasCollection->getTable('eav/attribute')),
                    'main_table.attribute_id=attribute.attribute_id',
                    'attribute_code');

        $this->_filterAliasByKey   = array();
        $this->_filterAliasById    = array();

        foreach ($aliasCollection as $alias) {
            $this->_filterAliasByKey[$alias->getAttributeCode()][$alias->getUrlKey()]  = $alias->getOptionId();
            $this->_filterAliasById[$alias->getAttributeCode()][$alias->getOptionId()] = $alias->getUrlKey();
        }

        if ($categories = $this->_getCategories()){
            foreach ($categories as $category) {
                $this->_filterAliasByKey['cat'][$category->getUrlKey()]    = $category->getEntityId();
                $this->_filterAliasById['cat'][$category->getEntityId()]    = $category->getUrlKey();
            }
        }

        return $this;
    }

    /**
     *
     * Return current filter value(s)
     *
     * @param string $requestVar
     * @return array|string|null
     */
    public function getValue($requestVar = '')
    {
        if ($requestVar === '') {
            return $this->_enabledFilters;
        } else {
            if (isset($this->_enabledFilters[$requestVar])) {
                return $this->_enabledFilters[$requestVar];
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function inPathEnabled()
    {
        return $this->_inPathEnabled;
    }
}
