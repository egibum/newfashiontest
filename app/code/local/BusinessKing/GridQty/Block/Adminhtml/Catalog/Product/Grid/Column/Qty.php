<?php

/**
 * Product grid quantity column
 *
 * @category   BusinessKing
 * @package    BusinessKing_FastShipment
 * @developer  Business King (http://www.businessapplicationking.com)
 */
class BusinessKing_GridQty_Block_Adminhtml_Catalog_Product_Grid_Column_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    public function setColumn($column)
    {
        $column->setEditable(true);
        return parent::setColumn($column);
    }

    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);
        $value = $value != '' ? $value : '&nbsp;';
        
        if (!$row->isComposite()) {
            $value .= '<input type="text" class="product-qty-input input-text validate-number" name="product_qty['.$row->getId().']" value="'.$this->_getInputValue($row).'"/>';
        }
				
        return $value;
    }

    public function renderExport(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
}