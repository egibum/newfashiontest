<?php

/**
 * Catalog product grid
 *
 * @category   BusinessKing
 * @package    BusinessKing_FastShipment
 * @developer  Business King (http://www.businessapplicationking.com)
 */
class BusinessKing_GridQty_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('gridqty/widget/grid.phtml');
    }
    
    /*protected function _prepareLayout()
    {
        $this->setChild('qty_save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Save Qty'),
                    'class'     => 'task',
                    'onclick'   => "return $('qty_save_form').submit();"
                ))
        );
        return parent::_prepareLayout();
    }*/

    protected function _toHtml()
    {
    	$html = '';
        //$html = '<form id="qty_save_form" method="post" action="'.$this->getUrl('*/gridqty/save').'"><input type="hidden" name="form_key" value="'.$this->getFormKey().'" />';
        $html .= parent::_toHtml();
        //$html .= '</form>';
        return $html;
    }

    /*public function getQtySaveButtonHtml()
    {
        return $this->getChildHtml('qty_save_button');
    }

    public function getMainButtonsHtml()
    {
        return $this->getQtySaveButtonHtml() . parent::getMainButtonsHtml();
    }*/

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->getColumn('qty')->setData('renderer','gridqty/adminhtml_catalog_product_grid_column_qty');
        return $this;
    }    
}