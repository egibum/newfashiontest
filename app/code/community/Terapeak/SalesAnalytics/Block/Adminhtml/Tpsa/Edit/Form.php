<?php

class Terapeak_SalesAnalytics_Block_Adminhtml_Tpsa_Edit_Form extends Mage_Adminhtml_Block_Widget_Container
{

        protected function _prepareLayout()
        {
                $this->setTemplate('salesanalytics/terapeak_salesanalytics_ui.phtml');
                return parent::_prepareLayout();
        }

}

?>
