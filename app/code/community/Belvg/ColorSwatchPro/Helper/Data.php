<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_ColorSwatchPro
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
?>
<?php
class Belvg_ColorSwatchPro_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isEnabled()
	{
		return Mage::getStoreConfig('colorswatch/settings/enabled');
	}
	
	public function getAttributesToReplace()
	{
		$data = array();
		$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
		$attributes   = explode(',', Mage::getStoreConfig('colorswatch/settings/attributes'));
		
		foreach ($attributes as $attributeCode) {
			$attribute = Mage::getModel('eav/entity_attribute');
			Mage::getResourceModel('eav/entity_attribute')->loadByCode($attribute, $entityTypeId, trim($attributeCode));
			if ($attribute->getAttributeId()) {
				$data[] = $attribute->getAttributeId();
			}
		}
		
		return $data;
	}
	
	public function useIcons()
	{
		return Mage::getStoreConfig('colorswatch/settings/use_icons');
	}
	
	public function showNotAllowed()
	{
		return Mage::getStoreConfig('colorswatch/settings/show_not_allowed');
	}
	
	public function getIconsDirectory()
	{
		return Mage::getBaseUrl('media') . Mage::getStoreConfig('colorswatch/settings/icon_folder');
	}
	
	public function getIconsExtension()
	{
		return Mage::getStoreConfig('colorswatch/settings/icon_ext');
	}
	
	public function getSwitchImage()
	{
		return Mage::getStoreConfig('colorswatch/settings/switch_image');
	}
	
	public function autoSelection()
	{
		return Mage::getStoreConfig('colorswatch/settings/auto_selection');
	}
	
	public function showOptions()
	{
		return Mage::getStoreConfig('colorswatch/category_page/show_options');
	}
	
	public function getImageSizeOnList()
	{
		return $this->_get_sizes(Mage::getStoreConfig('colorswatch/category_page/image_size'));
	}
	
	public function displayLink()
	{
		return Mage::getStoreConfig('colorswatch/category_page/display_link');
	}
	
	public function getLinkText()
	{
		return Mage::getStoreConfig('colorswatch/category_page/link_text');
	}
	
	public function getIconsQty()
	{
		return Mage::getStoreConfig('colorswatch/category_page/icons_qty');
	}
	
	public function getImageSizeOnView()
	{
		return $this->_get_sizes(Mage::getStoreConfig('colorswatch/product_page/image_size'));
	}
	
	public function getThumbSizeOnView()
	{
		return $this->_get_sizes(Mage::getStoreConfig('colorswatch/product_page/thumb_size'));
	}
	
	protected function _get_sizes($sizes)
	{
		preg_match_all('/\d+/', $sizes, $matches);
		$width  = isset($matches[0][0]) ? $matches[0][0] : NULL;
		$height = isset($matches[0][1]) ? $matches[0][1] : NULL;
		
		return array('width' => $width, 'height' => $height);
	}
	
}
