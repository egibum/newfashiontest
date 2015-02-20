<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_FeedSalidzini
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

class ET_FeedSalidzini_Block_Adminhtml_Xmlgrid_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('adminhtml_xmlfile_form');
        $this->setTitle(Mage::helper('feedsalidzini')->__('XML file Information'));
    }


    protected function _prepareForm()
    {
        $model = Mage::registry('feedsalidzini_feedsalidzini');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldSet = $form->addFieldset('add_feedsalidzini_form', array(
            'legend' => Mage::helper('feedsalidzini')->__('XML File options')
        ));

        if ($model->getId()) {
            $fieldSet->addField('xmlfile_id', 'hidden', array(
                'name' => 'xmlfile_id',
            ));
        }

        $fieldSet->addField('xmlfile_filename', 'text', array(
            'label' => Mage::helper('feedsalidzini')->__('Filename'),
            'name'  => 'xmlfile_filename',
            'required' => true,
            'note'  => Mage::helper('feedsalidzini')->__('example: products.xml'),
            'value' => $model->getXmlfileFilename()
        ));

        $fieldSet->addField('xmlfile_path', 'text', array(
            'label' => Mage::helper('feedsalidzini')->__('Path'),
            'name'  => 'xmlfile_path',
            'required' => true,
            'note'  => Mage::helper('feedsalidzini')->__(
                'example: "xmlfeeds/" or "/" for base path (path must be writable)'
            ),
            'value' => $model->getXmlfilePath()
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldSet->addField('store_id', 'select', array(
                'label'    => Mage::helper('feedsalidzini')->__('Store View'),
                'title'    => Mage::helper('feedsalidzini')->__('Store View'),
                'name'     => 'store_id',
                'required' => true,
                'note'  => Mage::helper('feedsalidzini')->__(
                    'Product attribute values will be taken from selected store view'
                ),
                'value'    => $model->getStoreId(),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm()
            ));
        } else {
            $fieldSet->addField('store_id', 'hidden', array(
                'name'     => 'store_id',
                'value'    => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldSet->addField('generate', 'hidden', array(
            'name'     => 'generate',
            'value'    => ''
        ));


        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
