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

class ET_FeedSalidzini_Block_Adminhtml_Xmlgrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('xmlfileGrid');
        $this->setDefaultSort('xmlfile_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('feedsalidzini/feedsalidzini')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('xmlfile_id', array(
            'header'    => Mage::helper('feedsalidzini')->__('ID'),
            'width'     => '50px',
            'index'     => 'xmlfile_id'
        ));

        $this->addColumn('xmlfile_filename', array(
            'header'    => Mage::helper('feedsalidzini')->__('Filename'),
            'index'     => 'xmlfile_filename'
        ));

        $this->addColumn('xmlfile_path', array(
            'header'    => Mage::helper('feedsalidzini')->__('Path'),
            'index'     => 'xmlfile_path'
        ));


        $this->addColumn('link', array(
            'header'    => Mage::helper('feedsalidzini')->__('External Link'),
            'index'     => 'concat(xmlfile_path, xmlfile_filename)',
            'renderer'  => 'feedsalidzini/adminhtml_xmlgrid_grid_renderer_link',
        ));

        $this->addColumn('xmlfile_time', array(
            'header'    => Mage::helper('feedsalidzini')->__('Last Time Generated'),
            'width'     => '150px',
            'index'     => 'xmlfile_time',
            'type'      => 'datetime',
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('feedsalidzini')->__('Store View'),
                'index'     => 'store_id',
                'type'      => 'store',
            ));
        }

        $this->addColumn('action', array(
            'header'   => Mage::helper('feedsalidzini')->__('Action'),
            'filter'   => false,
            'sortable' => false,
            'width'    => '100',
            'renderer' => 'feedsalidzini/adminhtml_xmlgrid_grid_renderer_action'
        ));


        return parent::_prepareColumns();
    }


    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('xmlfile_id' => $row->getId()));
    }

}
