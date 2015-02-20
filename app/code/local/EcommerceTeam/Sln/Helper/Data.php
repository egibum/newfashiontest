<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Helper_Data
    extends EcommerceTeam_Sln_Helper_Abstract
{
    const CONFIG_PATH             = 'catalog/layered_navigation';
    const ROBOTS_INDEX_FOLLOW     = '*';
    const ROBOTS_NOINDEX_NOFOLLOW = 'NOINDEX,NOFOLLOW';

    protected $_configCache = array();
    protected $_optionImageBasePath;
    protected $_optionImageBaseUrl;

    protected $_sliderJsLoaded = false;

    protected $_urlStyle;
    protected $_decimalUrlStyle;
    protected $_isSearch;

    public function __construct()
    {
        $this->_optionImageBasePath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'attribute' . DS;
        $this->_optionImageBaseUrl = Mage::getBaseUrl('media') . 'catalog' . '/' . 'attribute' . '/';
        $this->_isSearch = 0 !== preg_match('/^catalogsearch\\/result/i', trim(Mage::app()->getRequest()->getPathInfo(), '/'));
    }

    /**
     * @param $attributeCode
     * @return bool
     */
    protected function _isDecimal($attributeCode)
    {
        /** @var Mage_Eav_Model_Resource_Attribute_Collection $attributes */
        $attributes = $this->getLayer()->getFilterableAttributes();

        if ($attributes instanceof Varien_Data_Collection) {
            if ($attribute = $attributes->getItemByColumnValue('attribute_code', $attributeCode)) {
                return 'decimal' == $attribute->getBackendType();
            }
        }

        return false;
    }

    /**
     * Get config value
     *
     * @param string $xmlNode
     * @return string
     */
    public function getConfigData($xmlNode)
    {
        if(!isset($this->_configCache[$xmlNode])){
            $this->_configCache[$xmlNode] = Mage::getStoreConfig(self::CONFIG_PATH.'/'.$xmlNode);
        }
        return $this->_configCache[$xmlNode];
    }
    /**
     * Get config flag value
     *
     * @param string $xmlNode
     * @return boolean
     */
    public function getConfigFlag($xmlNode)
    {
        if(!isset($this->_configCache[$xmlNode])){
            $this->_configCache[$xmlNode] = Mage::getStoreConfigFlag(self::CONFIG_PATH.'/'.$xmlNode);
        }
        return $this->_configCache[$xmlNode];
    }

    /**
     *
     * Move option image from Temp directory to Media directory
     *
     * @param string $fileName
     * @param int $attributeId
     * @param int $optionId
     * @return string
     */
    public function moveImageFromTemp($fileName, $attributeId, $optionId)
    {
        $ioObject = new Varien_Io_File();
        $targetDirectory = $this->_optionImageBasePath . $attributeId . DS . $optionId;
        try {
            $ioObject->rmdir($targetDirectory, true);
            $ioObject->mkdir($targetDirectory, 0777, true);
            $ioObject->open(array('path'=>$targetDirectory));
        } catch (Exception $e) {
            return false;
        }

        $fileName   = trim($fileName, '.tmp');
        $targetFile = Varien_File_Uploader::getNewFileName($fileName);

        $path       = $targetDirectory . DS . $targetFile;
        $ioObject->mv(
            Mage::getSingleton('catalog/product_media_config')->getTmpMediaPath($fileName),
            $path
        );
        return $targetFile;
    }

    /**
     * @param $fileName
     * @param $attributeId
     * @param $optionId
     * @return bool|string
     */
    public function getOptionImageUrl($fileName, $attributeId, $optionId)
    {
        $subPath = $attributeId . DS . $optionId . DS . $fileName;
        $subURL  = $attributeId . '/' . $optionId . '/' . $fileName;
        if (is_file($this->_optionImageBasePath . $subPath)) {
            return $this->_optionImageBaseUrl . $subURL;
        }
        return false;
    }

    /**
     * @param $requestVar
     * @param $value
     * @param bool $singleMode
     * @return string
     */
    public function getCustomUrl($requestVar, $value, $singleMode = false)
    {
        return $this->_getItemUrl($requestVar, $value, $singleMode);
    }

    /**
     * @param Mage_Catalog_Model_Layer_Filter_Item $item
     * @param bool $singleMode
     * @return string
     */
    public function getUrl(Mage_Catalog_Model_Layer_Filter_Item $item, $singleMode = false)
    {
        $requestVar = $item->getFilter()->getRequestVar();
        $itemValue  = $item->getValue();

        return $this->_getItemUrl($requestVar, $itemValue, $singleMode);
    }


    /**
     * @return string
     */
    protected function _getOptionSeparator()
    {
        if (EcommerceTeam_Sln_Model_Url::URL_STYLE_OLD == $this->getUrlStyle()) {
            $optionSeparator = ',';
        } else {
            $optionSeparator = '-';
        }

        return $optionSeparator;
    }

    /**
     * @return array
     */
    protected function _getQueryParams()
    {
        return Mage::getSingleton('ecommerceteam_sln/request')->getQueryParams();
    }

    public function getClearUrl()
    {
        return $this->_getUri(array());
    }

    /**
     * @param $requestVar
     * @param string $valueToRemove
     * @return string
     */
    public function getRemoveUrl($requestVar, $valueToRemove = null)
    {
        /** @var $request EcommerceTeam_Sln_Model_Request */
        $request = Mage::getSingleton('ecommerceteam_sln/request');
        $values  = $request->getValue();
        if (empty($values)) {
            $values = array();
        }

        foreach ($values as $_requestVar => $requestValue) {
             if ($requestVar == $_requestVar) {
                 if (is_null($valueToRemove)) {
                     unset($values[$_requestVar]);
                 } else {
                     if (is_array($valueToRemove)) {
                         $valueToRemove = sprintf('%d-%d', $valueToRemove['index'], $valueToRemove['range']);
                     }
                     if (false !== ($key = array_search($valueToRemove, $requestValue))) {
                         unset($values[$_requestVar][$key]);
                         if (empty($values[$_requestVar])) {
                             unset($values[$_requestVar]);
                         }
                     }
                 }
                 break;
            }
        }

        return $this->_getUri($values);
    }

    /**
     * @param string $requestVar
     * @param string $itemValue
     * @param bool $singleMode
     * @return string
     */
    protected function _getItemUrl($requestVar, $itemValue, $singleMode)
    {
        /** @var EcommerceTeam_Sln_Model_Request $slnRequest */
        $slnRequest     = Mage::getSingleton('ecommerceteam_sln/request');
        $currentFilters = $slnRequest->getValue();
        if (empty($currentFilters)) {
            $currentFilters = array();
        }

        if (!$singleMode && isset($currentFilters[$requestVar])) {
            $currentFilters[$requestVar][] = $itemValue;
        } else {
            $currentFilters[$requestVar] = array($itemValue);
        }

        return $this->_getUri($currentFilters);
    }


    /**
     * @return mixed
     */
    public function getUrlSuffix()
    {
        if ($this->isSearch()) {
            return '';
        }
        return Mage::getStoreConfig('catalog/seo/category_url_suffix');
    }

    /**
     * @param array $filters
     * @return string
     */
    protected function _getUri(array $filters)
    {
        /** @var EcommerceTeam_Sln_Model_Request $slnRequest */
        $slnRequest     = Mage::getSingleton('ecommerceteam_sln/request');

        ksort($filters);

        $optionSeparator = $this->_getOptionSeparator();
        $urlStyle = $this->getUrlStyle();
        $urlStyleDecimal = $this->getUrlStyleDecimal();

        $parts = array(
            'attribute'  => array(),
            'query' => array(),
        );

        foreach ($filters as $_requestVar => $requestValue) {
            if ($this->_isDecimal($_requestVar)) {
                if (EcommerceTeam_Sln_Model_Url::URL_STYLE_IMPROVED == $urlStyle
                    && EcommerceTeam_Sln_Model_Url::URL_DECIMAL_STYLE_AS_ALL == $urlStyleDecimal) {
                    foreach ($requestValue as $idx => $_value) {
                        $requestValue[$idx] =  $_requestVar . '-' . $_value;
                    }
                }
                if (EcommerceTeam_Sln_Model_Url::URL_DECIMAL_STYLE_QUERY == $urlStyleDecimal) {
                    $parts['query'][$_requestVar] = implode($optionSeparator, $requestValue);
                } else {
                    $parts['attribute'][$_requestVar] = implode($optionSeparator, $requestValue);
                }
            } else {
                foreach ($requestValue as $key => $value) {
                    if ($_value = $slnRequest->getFilterKeyById($_requestVar, $value)) {
                        $requestValue[$key] = $_value;
                    }
                }
                sort($requestValue);
                $parts['attribute'][$_requestVar] = implode($optionSeparator, $requestValue);
            }
        }

        return $this->_buildUri($parts['attribute'], $parts['query']);
    }

    /**
     * @param array $filters
     * @param array $query
     * @return string
     */
    protected function _buildUri(array $filters, array $query = array())
    {
        $query = array_merge($this->_getQueryParams(), $query);

        /** @var $slnRequest EcommerceTeam_Sln_Model_Request */
        $slnRequest = Mage::getSingleton('ecommerceteam_sln/request');
        /** @var $helper EcommerceTeam_Sln_Helper_Data */
        $helper  = Mage::helper('ecommerceteam_sln');
        $baseUrl = $slnRequest->getBaseUrl();

        switch ($helper->getUrlStyle()) {
            case EcommerceTeam_Sln_Model_Url::URL_STYLE_IMPROVED:
                foreach ($query as $key => $value) {
                    $queryStr[] = "{$key}={$value}";
                }
                $uri = sprintf('%s%s%s%s',
                    trim($baseUrl, '/'),
                    empty($filters) ? '' : '/' . implode('-', $filters),
                    $this->getUrlSuffix(),
                    (empty($queryStr) ? '' : '?' . implode('&', $queryStr))
                );
                break;
            case EcommerceTeam_Sln_Model_Url::URL_STYLE_OLD:
                $urlSeparator = $helper->getConfigData('url_separator');
                $filterParamsSeparated = array();
                foreach (array_merge($query, $filters) as $key => $value) {
                    $filterParamsSeparated[] = $key;
                    $filterParamsSeparated[] = $value;
                }
                if (empty($filterParamsSeparated)) {
                    $uri = sprintf('%s%s%s',
                        trim($baseUrl, '/'),
                        $this->getUrlSuffix(),
                        (empty($queryStr) ? '' : '?' . implode('&', $queryStr))
                    );
                } else {
                    $uri = sprintf('%s/%s/%s%s%s',
                        trim($baseUrl, '/'),
                        $urlSeparator,
                        implode('/', $filterParamsSeparated),
                        $this->getUrlSuffix(),
                        (empty($queryStr) ? '' : '?' . implode('&', $queryStr))
                    );
                }
                break;
            case EcommerceTeam_Sln_Model_Url::URL_STYLE_QUERY:
                $queryStr = array();
                foreach (array_merge($query, $filters) as $key => $value) {
                    $queryStr[] = "{$key}={$value}";
                }
                $uri = sprintf('%s%s%s',
                    trim($baseUrl, '/'),
                    $this->getUrlSuffix(),
                    empty($queryStr) ? '' : '?' . implode('&', $queryStr)
                );
                break;
        }

        return $uri;
    }

    /**
     * @return bool
     */
    public function isMagentoEnterprise()
    {
        return (bool) Mage::getConfig()->getModuleConfig('Enterprise_Enterprise');
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return Mage::getSingleton('ecommerceteam_sln/layer')->getPageTitle();
    }

    /**
     * @return string
     */
    public function getUrlStyle()
    {
        if (is_null($this->_urlStyle)) {
            if ($this->isSearch()) { // Seo url can't be used for search
                $this->_urlStyle = EcommerceTeam_Sln_Model_Url::URL_STYLE_QUERY;
            } else {
                $this->_urlStyle = Mage::getStoreConfig('catalog/layered_navigation/url_style');
            }
        }

        return $this->_urlStyle;
    }

    /**
     * @return bool
     */
    public function isSearch()
    {
        return $this->_isSearch;
    }

    /**
     * @return string
     */
    public function getUrlStyleDecimal()
    {
        if (is_null($this->_decimalUrlStyle)) {
            $this->_decimalUrlStyle = Mage::getStoreConfig('catalog/layered_navigation/url_style_decimal');
        }

        return $this->_decimalUrlStyle;
    }

    /**
     * @return string
     */
    public function getPageCmsBlock()
    {
        $cmsBlock = Mage::getSingleton('ecommerceteam_sln/layer')->getCmsBlock();
        $html = '';
        if ($cmsBlock) {
            $cmsBlock = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($cmsBlock);
            if ($cmsBlock) {
                $html = $cmsBlock->toHtml();
            }
        }

        return $html;
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isAjaxRequest(Mage_Core_Controller_Request_Http $request = null)
    {
        if (is_null($request)) {
            $request = Mage::app()->getRequest();
        }
        return 'XMLHttpRequest' == $request->getHeader('X-Requested-With');
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function forceLayered(Mage_Core_Controller_Request_Http $request = null)
    {
        if (is_null($request)) {
            $request = Mage::app()->getRequest();
        }

        if (EcommerceTeam_Sln_Model_Url::URL_STYLE_QUERY == $this->getUrlStyle()) {
            return false;
        }

        return $this->isAjaxRequest() && $request->getHeader('forceLayered');
    }
}
