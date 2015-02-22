<?php 
class Newfashion_Morefrom_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$id = $this->getRequest()->getParam('id');
		$page = $this->getRequest()->getParam('page');
		if($id) {
			
			$_category = Mage::getModel('catalog/category')->load($id);
			$product_count = $_category->getProductCount();
			$product = Mage::getModel('catalog/product');

			//load the category's products as a collection
			$_productCollection = $product->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToSort('created_at', 'DESC')
			    ->setCurPage($page) // 2nd page
				->setPageSize(16)
				->addCategoryFilter($_category)
				->load();

			// build an array for conversion
			$json_products = array();
			foreach ($_productCollection as $_product) {
				$_product->getData();
				
				$images_obj = Mage::helper('catalog/image')->init($_product, 'small_image')->resize(370, 370);
				$images = (string)$images_obj;
				$name = $_product->getName();
				$url = $_product->getProductUrl();
				$price = $_product->getFormatedPrice();
				$html .= "<li class='item'>";
				$html .= "<div class='regular'>";
				$html .=	"<a href='".$url."' title='".$name."' class='product-image'>";
				$html .= 	 "<img src='".$images ."' width='370' height='370' alt='".$name."' />";
				$html .= 	"</a>";
				$html .= "<div class='product-info'>";
				$html .=	"<div class='button-container'>";
					if(!$_product->isSaleable()) {
						$html .="<p class='availability out-of-stock'><span>Out of stock</span></p>";
					}
				$html .=		"</div>";
				$html .=		"<a class='product-name' href='".$url."' title='".$name."'>".$name."</a>";
				$html .= "<div class='price-box'>";
				$html .= "<span class='regular-price'>";
				$html .= "<span class='price'>";
				$html .= $price;
				$html .= "</span>";
				$html .= "</span>";
				$html .= "</div>";
				$html .= "</div>";
				$html .= "</div>";
				$html .= "</li>";
			}
			if (($page*15) <= $product_count) {
				$html .= "<li class='item page-".($page+1)." more-items-item'>";
				$html .= "<div class='more-items' onclick='getMoreProducts(".$id.",".($page+1).");'>";
				$html .= "<div class='more-items-image'></div>";
				$html .= "<div class='title'>Load 15 more items</div>";
				$html .= "</div>";
				$html .= "<div class='itemCount'>Items ".(($page-1)*15)." to ".($page*15)." of ".$product_count." total</div>";
				$html .= "</li>";
			}


			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
		} 
	}
}