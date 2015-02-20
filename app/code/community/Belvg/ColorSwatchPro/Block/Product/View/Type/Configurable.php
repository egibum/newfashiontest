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
class Belvg_ColorSwatchPro_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
	protected $_config_data = array();
	
	public function getJsonConfigData()
	{
		if (empty($this->_config_data)) {
			$this->_config_data = array(
				'attributes'     => $this->helper('colorswatch')->getAttributesToReplace(),
				'show_not_allowed' => $this->helper('colorswatch')->showNotAllowed(),
				'use_icons'      => $this->helper('colorswatch')->useIcons(),
				'icon_folder'    => $this->helper('colorswatch')->getIconsDirectory(),
				'icon_ext'       => $this->helper('colorswatch')->getIconsExtension(),
				'switch_image'   => $this->helper('colorswatch')->getSwitchImage(),
				'auto_selection' => $this->helper('colorswatch')->autoSelection(),
				'page_type'	 	 => 2, // 1 - list, 2 - view
				'original_size'  => 1,
				'reload_gallery' => 1,
				'swatch_url'     => Mage::getUrl('colorswatch/actions/images'),
			);
		}
		$this->_config_data['product_id'] = $this->getProduct()->getId();
		return Mage::helper('core')->jsonEncode($this->_config_data);
	}
}
