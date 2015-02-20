<?php
/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */


class EcommerceTeam_Sln_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Options extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ecommerceteam/catalog/product/attribute/options.phtml');
    }

    public function getOptionValues()
    {
        $values = parent::getOptionValues();
        if($values){
            $attribute_id = $this->getAttributeObject()->getId();
            $options = Mage::getResourceModel('ecommerceteam_sln/attribute_collection')->addFieldToFilter('attribute_id', $attribute_id);
            foreach($values as $value){
                if($data = $options->getItemByColumnValue('option_id', $value['id'])){
                    $value->addData(
                        array(
                            'url_key'       => $data->getUrlKey(),
                            'image_info'    => array(array('name'=>$data->getImage())),
                        )
                    );
                }else{
                    $value->addData(
                        array(
                            'url_key'       => '',
                            'image_info'    => array(array('name'=>'')),
                        )
                    );
                }
            }
        }
        return $values;
    }
}
