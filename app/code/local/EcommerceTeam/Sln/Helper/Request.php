<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */


class EcommerceTeam_Sln_Helper_Request
    extends Mage_Core_Helper_Abstract
{
    protected $_categories;

    /**
     * @param string $requestStr
     * @return Varien_Object
     * @throws EcommerceTeam_Sln_Exception_Rewrite
     */
    protected function _extractCategoryInformation(&$requestStr)
    {
        if (preg_match('/^(\/[\w\-\.\\/]+)+?(\\/[\w\-\.]+)$/', $requestStr, $matches)) {
            $helper = Mage::helper('ecommerceteam_sln');
            $categoryPath = trim($matches[1], '/') . $helper->getUrlSuffix();
            $requestStr = $matches[2];

            /** @var $urlRewriteModel Mage_Core_Model_Url_Rewrite */
            $urlRewriteModel = Mage::getModel('core/url_rewrite');
            if (Mage::helper('ecommerceteam_sln')->isMagentoEnterprise()) {
                $urlRewriteModel = Mage::getModel('enterprise_urlrewrite/url_rewrite');
            }

            /** @var Mage_Core_Model_Resource_Url_Rewrite_Collection $collection */
            $collection = $urlRewriteModel->getCollection();
            $collection->addFieldToFilter('request_path', $categoryPath);
            $collection->addFieldToFilter('category_id', array('notnull' => true));
            $collection->addFieldToFilter('product_id', array('null' => true));
            $collection->addFieldToFilter('is_system', true);
            $collection->setPageSize(1);
            $collection->addStoreFilter(Mage::app()->getStore()->getId());

            if (count($collection)) {
                return $collection->getFirstItem();
            }
        }

        throw new EcommerceTeam_Sln_Exception_Rewrite($this->__("Can't resolve category request path."));
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
     * @param $str
     * @return mixed
     */
    public function clearSuffix($str)
    {
        $suffix  = Mage::helper('ecommerceteam_sln')->getUrlSuffix();
        if ($suffix) {
            $suffix  = addcslashes($suffix, '/.-');
            $str = preg_replace("/^(.+?)({$suffix})$/ui", '$1', $str);
        }

        return $str;
    }

    /**
     * @param Zend_Controller_Request_Http $request
     * @return Zend_Controller_Request_Http
     * @throws EcommerceTeam_Sln_Exception_Rewrite
     */
    protected function _improvedRewrite(Zend_Controller_Request_Http $request)
    {
        $urlSuffix = Mage::helper('ecommerceteam_sln')->getUrlSuffix();
        if (preg_match('/^(\/[\w\-\.\\/]+)+?(\\/[\w\-\.]+)'.addcslashes($urlSuffix, './').'$/', $request->getPathInfo())) { // Looks like layered request
            $pathInfo = $request->getPathInfo();
            $pathInfo = Mage::helper('ecommerceteam_sln/request')->clearSuffix($pathInfo);

            /** @var Mage_Core_Model_Url_Rewrite $urlRewriteObject */
            $urlRewriteObject = $this->_extractCategoryInformation($pathInfo);
            $currentCategory = Mage::getModel('catalog/category')->load($urlRewriteObject->getData('category_id'));
            Mage::getSingleton('catalog/layer')->setCurrentCategory($currentCategory);

            $request->setParam('layered_navigation_base_url', Mage::getUrl() . $urlRewriteObject->getRequestPath());
            $request->setParam('layered_navigation_path', $pathInfo);
            $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, trim($request->getPathInfo(), '/'));
            $request->setPathInfo($urlRewriteObject->getTargetPath());
        }

        return $request;
    }

    /**
     * @param Zend_Controller_Request_Http $request
     * @return $this|Zend_Controller_Request_Http
     * @throws EcommerceTeam_Sln_Exception_Rewrite
     */
    protected function _oldRewrite(Zend_Controller_Request_Http $request)
    {
        /** @var $helper EcommerceTeam_Sln_Helper_Data */
        $helper = Mage::helper('ecommerceteam_sln');

        $separator   = $helper->getConfigData('url_separator');
        $requestPath = trim($request->getPathInfo(), '/');
        $requestPath = Mage::helper('ecommerceteam_sln/request')->clearSuffix($requestPath);

        $path        = explode('/', $requestPath);
        $filterKey   = array_search($separator, $path);
        if ($filterKey !== false) {
            $filterParams = array_slice($path, $filterKey+1);
            if (count($filterParams)%2) {
                array_unshift($filterParams, 'attributes');
            }
            if ($filterKey) {
                $categoryUrlKey = implode('/', array_slice($path, 0, $filterKey));
            } else {
                $categoryUrlKey = implode('/', $path);
            }
            if ($filterKey && $categoryUrlKey) {
                if (Mage::getStoreConfig("catalog/seo/category_url_suffix") == "/") {
                    $categoryUrlKey = $categoryUrlKey . '/';
                } else {
                    $categoryUrlKey = trim($categoryUrlKey, '/') . $helper->getUrlSuffix();
                }

                /** @var $urlRewriteModel Mage_Core_Model_Url_Rewrite */
                $urlRewriteModel = Mage::getModel('core/url_rewrite');
                if ($helper->isMagentoEnterprise()) {
                    $urlRewriteModel = Mage::getModel('enterprise_urlrewrite/url_rewrite');
                }

                $urlRewriteModel->setData('store_id', Mage::app()->getStore()->getId());
                $targetPath = $urlRewriteModel->loadByRequestPath(array($categoryUrlKey))->getTargetPath();
                if (!$urlRewriteModel->getData('category_id')) {
                    throw new EcommerceTeam_Sln_Exception_Rewrite($this->__("Can't resolve category request path."));
                }
                $currentCategory = Mage::getModel('catalog/category')->load($urlRewriteModel->getData('category_id'));
                Mage::getSingleton('catalog/layer')->setCurrentCategory($currentCategory);

                $itemsLength = count($filterParams);

                $request->setParam('layered_navigation_base_url', Mage::getBaseUrl('web') . $urlRewriteModel->getRequestPath());

                for ($i = 0; $i < $itemsLength; $i+=2) {
                    $request->setParam($filterParams[$i], isset($filterParams[$i+1]) ? explode(',', $filterParams[$i+1]) : null);
                }

                if ($targetPath) {
                    $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, trim($request->getPathInfo(), '/'));
                    $request->setPathInfo($targetPath);
                    return $request;
                }
            }
        }

        return $this;
    }

    /**
     * @param Mage_Core_Controller_Request_Http|Zend_Controller_Request_Http $request
     * @return Mage_Core_Controller_Request_Http
     */
    public function rewrite(Zend_Controller_Request_Http $request)
    {
        try {
            /** @var $helper EcommerceTeam_Sln_Helper_Data */
            $helper = Mage::helper('ecommerceteam_sln');

            if ($request->getAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS)) {
                return false;
            }

            switch ($helper->getUrlStyle()) {
                case EcommerceTeam_Sln_Model_Url::URL_STYLE_IMPROVED:
                    $this->_improvedRewrite($request);
                    break;
                case EcommerceTeam_Sln_Model_Url::URL_STYLE_OLD:
                    $this->_oldRewrite($request);
                    break;
            }
        } catch (EcommerceTeam_Sln_Exception_Rewrite $e) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getClearUrl()
    {
        if(Mage::helper('ecommerceteam_sln')->isSearch()){
            $params['_query']       = array('q' => strval(Mage::app()->getRequest()->getParam('q')));
            $params['_escape']      = true;

            return Mage::getUrl('*/*/*', $params);
        }

        return Mage::helper('ecommerceteam_sln')->getClearUrl();
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection|bool
     */
    public function getCategories()
    {
        if (is_null($this->_categories)) {
            if ($currentCategory = Mage::getSingleton('catalog/layer')->getCurrentCategory()){
                /** @var Mage_Catalog_Model_Resource_Category $resource */
                $resource = $currentCategory->getResource();
                $childIds = $resource->getChildren($currentCategory);
                if (!empty($childIds)){
                    $collection = $currentCategory->getCollection()->addIdFilter($childIds);
                    $collection->addAttributeToSelect('name');
                    $collection->addAttributeToSelect('url_key');
                    $this->_categories = $collection;
                } else {
                    $this->_categories = false;
                }
            }
        }

        return $this->_categories;
    }
}
