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
class Belvg_ColorSwatchPro_ActionsController extends Mage_Core_Controller_Front_Action
{
	public $thumb_width  = NULL;
	public $thumb_height = NULL;
	
	public function imagesAction()
	{
		$post = Mage::app()->getRequest()->getPost();
		
		switch ($post['page_type']) {
			case 1:
				$sizes  = Mage::helper('colorswatch')->getImageSizeOnList();
				$width  = $sizes['width']; $height = $sizes['height'];
				break;
			
			case 2:
				$sizes  = Mage::helper('colorswatch')->getImageSizeOnView();
				$width  = $sizes['width']; $height = $sizes['height'];
				$thumbs = Mage::helper('colorswatch')->getThumbSizeOnView();
				$this->thumb_width = $thumbs['width']; $this->thumb_height = $thumbs['height'];
				break;
			
			default:
				break;
		}
		
		if (isset($post['product_id']) AND $_product = Mage::getModel('catalog/product')->load($post['product_id'])) {
			$data = array();
			$allProducts =  $_product->getTypeInstance(true)
				->getUsedProducts(NULL,  $_product);
			
			foreach ($allProducts as $product) {
				if ($product->isSaleable()) {
					$data[$product->getId()]['image'] = array(
						'src' => (string) Mage::helper('catalog/image')->init($product, 'image')->resize($width, $height),
						'original' => (string) Mage::helper('catalog/image')->init($product, 'image'),
						'gallery' => $post['reload_gallery'] ? $this->_getGalleryImages($product) : array(),
					);
				}
			}
			
		}
		header('Content-type: application/json');
		echo Mage::helper('core')->jsonEncode($data);
		exit;
	}
	
	protected function _getGalleryImages($product)
	{
		$images = array();
		$product = Mage::getModel('catalog/product')->load($product->getId());
		$collection = $product->getMediaGalleryImages();
		if($collection->getSize()) {
			foreach($collection as $_item) {
				$images[] = array(
					'src' => (string) Mage::helper('catalog/image')->init($product, 'image', $_item->getFile())->resize($this->thumb_width, $this->thumb_height),
					'original' => (string) Mage::helper('catalog/image')->init($product, 'image', $_item->getFile()),
					'label' => $_item->getLabel(),
				);
			}
		}
		return $images;
	}
}
