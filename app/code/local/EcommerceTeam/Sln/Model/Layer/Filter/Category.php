<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */


class EcommerceTeam_Sln_Model_Layer_Filter_Category
    extends Mage_Catalog_Model_Layer_Filter_Category
{

    protected $_allItems;

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $_request = Mage::getSingleton('ecommerceteam_sln/request');
        $filters  = $_request->getValue('cat');

        if (empty($filters)) {
            return $this;
        }

        $value = array();

        foreach ($filters as $filter) {
            $category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($filter);

            if ($this->_isValidCategory($category)) {
                $value[] = $filter;


                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($category->getName(), $filter)
                );
            }
        }

        if (!empty($value)) {
            $this->_getResource()->applyFilterToCollection($this, $value);
        }

        return $this;
    }

    /**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $data     = $this->_getItemsData(true);
        $allItems = array();
        $items    = array();

        foreach ($data as $itemData) {
            $item = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['level'],
                $itemData['is_selected']
            );
            $allItems[] = $item;
            if (!$item->getIsSelected()){
                $items[] = $item;
            }
        }

        $this->_items    = $items;
        $this->_allItems = $allItems;
        return $this;
    }

    /**
     * @param $parentCategory
     * @param $selected
     * @param $level
     * @return array
     */
    protected function _prepareCategories($parentCategory,$selected,$level)
    {
        if ($level !== 0){
            $categoriesFinal = array($parentCategory->getId());
        }
        else{
            $categoriesFinal = array();
        }
        $categories = $parentCategory->getChildrenCategories();
        if (count($categories)){

            $canShow = false;

            if ($level == 0){ //show subcats for root category
                $canShow = true;
            }

            if (in_array($parentCategory->getId(), $selected)){ //show subcats for selected category
                $canShow = true;
            }

            foreach ($categories as $category){ //show subcats if any subcategory is selected
                if (in_array($category->getId(), $selected)){
                    $canShow = true;
                }
                $chidrenCats = $category->getChildrenCategories();
                if (is_array($chidrenCats)) {
                    $chidrenCatsIds = array_keys($chidrenCats);
                } else {
                    $chidrenCatsIds = $category->getChildrenCategories()->getAllIds();
                }
                if (array_intersect($chidrenCatsIds, $selected)) {
                    $canShow = true;
                }
            }

            if ($canShow){
                foreach ($categories as $category){
                    if ($category->hasChildren()){
                        $categoriesFinal = array_merge($categoriesFinal, $this->_prepareCategories($category,$selected,$level+1));
                    }else{
                        $categoriesFinal[] = $category->getId();
                    }
                }
            }
        }

        return $categoriesFinal;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {

        if (!Mage::helper('ecommerceteam_sln')->getConfigFlag('enable_category')) {
            return array();
        }

        $key  = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            /** @var $currentCategory Mage_Catalog_Model_Category */
            $currentCategory = $this->getLayer()->getCurrentCategory();
            $currentLevel = $currentCategory->getLevel() + 1;
            
            $request         = Mage::getSingleton('ecommerceteam_sln/request');
            $selected        = array();

            if ($value = $request->getValue($this->_requestVar)) {
                $selected = array_merge($selected, $value);
            }

            $categories = $this->_prepareCategories($currentCategory,$selected,0);
            $data = array();
            if (count($categories) > 0) {
                $categoryCount = $this->_getResource()->getFullCount($this, $categories);
                foreach ($categories as $category) {
                    $category = Mage::getModel("catalog/category")->load($category);
                    if ($category->getIsActive()
                        && isset($categoryCount[$category->getId()])
                        && $categoryCount[$category->getId()] > 0 || in_array($category->getId(), $selected)) {
                        $value = $category->getId();
                        $data[] = array(
                            'label' => Mage::helper('core')->htmlEscape($category->getName()),
                            'value' => $value,
                            'count' => isset($categoryCount[$category->getId()]) ? $categoryCount[$category->getId()] : 0,
                            'is_selected' => in_array($category->getId(), $selected),
                            'level' => ($category->getLevel() - $currentLevel)
                        );
                    }
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Category
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('catalog/layer_filter_category');
        }
        return $this->_resource;
    }

    /**
     * Get all fiter items count
     *
     * @return int
     */
    public function getAllItems()
    {
        if (is_null($this->_allItems)) {
            $this->_initItems();
        }
        return $this->_allItems;
    }

    /**
     * Get all fiter items count
     *
     * @return int
     */
    public function getAllItemsCount()
    {
        return count($this->getAllItems());
    }

    /**
     * Get frontend type for filter
     *
     * @return null|string
     */
    public function getFrontendType()
    {
        return Mage::helper('ecommerceteam_sln')->getConfigData('cat_filter_type');
    }

    /**
     * @param $label
     * @param $value
     * @param int $count
     * @param int $level
     * @param bool $isSelected
     * @return mixed
     */
    protected function _createItem($label, $value, $count = 0, $level = 0, $isSelected = false)
    {
        return Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setLevel($level)
            ->setIsSelected($isSelected);
    }
}
