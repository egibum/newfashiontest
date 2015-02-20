<?php

/**
 * Grid quantity controller
 *
 * @category   BusinessKing
 * @package    BusinessKing_GridQty
 * @developer  Business King (http://www.businessapplicationking.com)
 */
class BusinessKing_GridQty_Adminhtml_GridqtyController extends Mage_Adminhtml_Controller_Action
{
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('product_qty');
        $errors = array();
        if (count($data)>0) {
	        foreach ($data as $productId => $qty) {
	            try {
	                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
	                if ($stockItem) {
	                    $stockItem->setQty($qty);
						if ($qty > 0) {
	                		$stockItem->setIsInStock(1);
	                	}
	                	else {
	                		$stockItem->setIsInStock(0);
	                	}
	                }
	                $stockItem->save();
	            } catch (Mage_Core_Exception $e) {
	                Mage::logException($e);
	                $this->_getSession()->addException($e->getMessage());
	                $errors[] = $e->getMessage();
	            } catch (Exception $e) {
	                Mage::logException($e);
	                $this->_getSession()->addException($this->__('Quantity could not save for product id: %d', $productId));
	                $errors[] = $e->getMessage();
	            }
	        }

	        if (count($errors) == 0) {
	            $this->_getSession()->addSuccess('Quantity saved successfully');
	        }
        }

        $this->_redirectReferer();
    }
}