<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

class EcommerceTeam_Sln_Model_Observer
{
    protected $_categories;

    /**
     * Check for ajax request
     *
     * @return $this
     */
    public function initAjax()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getSingleton('core/layout');
        if ($layout) {
            if (Mage::helper('ecommerceteam_sln')->isAjaxRequest()) {
                Mage::app()->getFrontController()->getResponse()->setHeader('content-type', 'application/json');
                $layout->removeOutputBlock('root');

                $blocks = $layout->getAllBlocks();
                $layeredBlocks = array_filter($blocks, create_function('$block', 'if(preg_match("/^ecommerceteam_seo_navigation\w*$/i", $block->getNameInLayout()) > 0) { return true; }'));

                $productListBlock = $layout->getBlock('product_list');
                if(!$productListBlock){
                    $productListBlock = $layout->getBlock('search_result_list');
                }
                $headBlock = $layout->getBlock('head');
                $title = '';
                if ($headBlock){
                    $title = $headBlock->getTitle();
                }
                if (count($layeredBlocks) > 0 && $productListBlock) {
                    $layeredEnabled = 0;
                    if (Mage::getSingleton('ecommerceteam_sln/request')->inPathEnabled()) {
                        $layeredEnabled = 1;
                    }
                    $slnAjaxContainer = $layout->createBlock('core/template', 'ecommerceteam_sln_ajax');
                    $slnAjaxContainer->setData('layered_enabled', $layeredEnabled);
                    $slnAjaxContainer->setData('layered_base_url', Mage::helper('ecommerceteam_sln/request')->getClearUrl());

                    $layeredBlocksHtml = array();
                    foreach ($layeredBlocks as $block) {
                        $block->setIsAjax(true);
                        $layeredBlocksHtml[$block->getBlockId()] = array(
                            'html'   => $block->toHtml(),
                            'script' => $block->getJavaScript()
                        );
                    }
                    $slnAjaxContainer->setData('navigation_block_html', $layeredBlocksHtml);
                    $slnAjaxContainer->setData('product_list_block_html', $productListBlock->toHtml());
                    $slnAjaxContainer->setData('page_title', $title);
                    $layout->addOutputBlock('ecommerceteam_sln_ajax', 'toJson');
                }
            }
        }

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategories()
    {
        if (is_null($this->_categories)) {
            if ($currentCategory = Mage::registry('current_category')) {
                $childIds = $currentCategory->getResource()->getChildren($currentCategory, false);
                if (!empty($childIds)) {
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

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function setPageTitle($observer)
    {
        $moduleName        = Mage::app()->getFrontController()->getRequest()->getModuleName();
        $controllerName    = Mage::app()->getFrontController()->getRequest()->getControllerName();
        $store             = Mage::app()->getStore();
        /** @var $helper EcommerceTeam_Sln_Helper_Data */
        $helper = Mage::helper('ecommerceteam_sln');

        if (!Mage::registry('layer_loaded') || $moduleName == 'catalogsearch' && $controllerName == 'advanced') {
            return;
        }
        if ($headBlock = $observer->getLayout()->getBlock('head')) {
            $headBlock->setRobots($helper->getConfigData('robots'));
            $url = Mage::app()->getRequest()->getRequestString();
            $metaData = '';
            if ($url){
                if (substr($url,-1) == '/'){
                    $url = substr($url,0,strlen($url)-1);
                }
                $metaData = Mage::getModel('ecommerceteam_sln/meta')->load($url,'url');
            }
            if ($metaData && $metaData->getId()) {
                if ($metaData->getMetaTitle()) {
                    $headBlock->setTitle($metaData->getMetaTitle());
                }
                if ($metaData->getMetaKeywords()) {
                    $headBlock->setKeywords($metaData->getMetaKeywords());
                }
                if ($metaData->getMetaDescription()) {
                    $headBlock->setDescription($metaData->getMetaDescription());
                }
                if ($metaData->getPageTitle()) {
                    Mage::getSingleton('ecommerceteam_sln/layer')->setPageTitle($metaData->getPageTitle());
                }
                if ($metaData->getCmsBlock()) {
                   Mage::getSingleton('ecommerceteam_sln/layer')->setCmsBlock($metaData->getCmsBlock());
                }
                if ($metaData->getRobots()) {
                    $headBlock->setRobots($metaData->getRobots());
                }
            } else {
                $_request = Mage::getSingleton('ecommerceteam_sln/request');
                $activeFilters = $_request->getValue();

                if (!empty($activeFilters)) {
                    $attributes = $_request->getFilterableAttributes();
                    if (!empty($attributes) && ($title = $headBlock->getTitle())) {
                        $title = str_replace(Mage::getStoreConfig('design/head/title_suffix'),"",$title);
                        $filtersTitle = array();
                        foreach ($activeFilters as $code=>$value) {
                            $_labelValues = array();
                            if ($model = $attributes->getItemByColumnValue('attribute_code', $code)) {
                                $_labelValues = array();
                                if ($model->getFrontendInput() == 'select' || $model->getFrontendInput() == 'multiselect') {
                                    foreach ((array)$value as $_value) {
                                        $_labelValues[] = ($model->getSource()->getOptionText($_value));
                                    }
                                } elseif ($model->getFrontendInput() == 'price') {
                                    if (isset($value['start']) && isset($value['end'])) {
                                       $_labelValues[] = Mage::helper('core')->currency($value['start'], true, false) .' - '. Mage::helper('core')->currency($value['end'], true, false);
                                    }
                                    foreach ((array) $value as $_value) {
                                        if (isset($_value['start']) && isset($_value['end'])) {
                                            $_labelValues[] = $store->formatPrice(($_value['start']-1)*$_value['end'],false) .' - '. $store->formatPrice(($_value['start'])*$_value['end'],false);
                                        }
                                    }
                                }

                                $filtersTitle[] = $model->getStoreLabel() . ': ' . implode(', ', $_labelValues);

                            } elseif ($code == 'cat') {
                                $categories = $this->getCategories();
                                $_labelValues = array();

                                if ($categories && !empty($categories)) {

                                    foreach ((array)$value as $id) {
                                        if ($cat = $categories->getItemById($id)){
                                            $_labelValues[] = $cat->getName();
                                        }
                                    }
                                    $title = trim(implode(', ', $_labelValues)). ' - ' .  $title;
                                }
                            }
                        }
                        if ($filtersTitle) {
                            $headBlock->setTitle($title.' with '. implode(' and ', $filtersTitle));
                        } else {
                            $headBlock->setTitle($title);
                        }
                    }
                    if (($category = Mage::registry('current_category')) && !$helper->getConfigData('use_category_canonical')) {
                        $headBlock->removeItem('link_rel',$category->getUrl());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function loadAttributeData($observer)
    {
        $attribute    = $observer->getAttribute();
        $attribute_id = (int)$attribute->getAttributeId();
        $connection   = Mage::getSingleton('core/resource')->getConnection('read');
        $table        = Mage::getSingleton('core/resource')->getTableName('ecommerceteam_sln_attribute_data');

        $select = new Varien_Db_Select($connection);
        $select->from($table, array('group_id', 'frontend_type', 'comment', 'options_limit'));
        $select->where('attribute_id = ?', $attribute_id);

        $data = $connection->fetchRow($select);

        if ($data && is_array($data) && !empty($data)) {
            $attribute->addData($data);
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function saveAttributeData($observer)
    {
        /** @var $connection Varien_Db_Adapter_Pdo_Mysql */
        $connection  = Mage::getSingleton('core/resource')->getConnection('read');
        $table       = Mage::getSingleton('core/resource')->getTableName('ecommerceteam_sln_attribute_data');

        $attributeId     = (int)$observer->getAttribute()->getAttributeId();
        $groupId         = addslashes($observer->getAttribute()->getData('group_id'));
        $frontendType    = addslashes($observer->getAttribute()->getData('frontend_type'));
        $comment         = $observer->getAttribute()->getData('comment');
        $optionsLimit    = $observer->getAttribute()->getData('options_limit');

        $connection->insertOnDuplicate(
            $table,
            array (
                'attribute_id' => $attributeId,
                'group_id' => $groupId,
                'frontend_type' => $frontendType,
                'comment' => $comment,
                'options_limit' => $optionsLimit
            )
        );

        return $this;
    }
}
