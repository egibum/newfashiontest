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

class ET_FeedSalidzini_Adminhtml_XmlgridController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Catalog'),
                Mage::helper('adminhtml')->__('ET IP Security log')
            );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    /**
     * Create new xml file
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit xml file
     */
    public function editAction()
    {
        $this->_title($this->__('Catalog'))->_title($this->__('Feed Salidzini'));
        // 1. Get ID and create model
        $id    = $this->getRequest()->getParam('xmlfile_id');
        $model = Mage::getModel('feedsalidzini/feedsalidzini');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('feedsalidzini')->__('This xml file no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getXmlfileFilename() : $this->__('New Xml File'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('feedsalidzini_feedsalidzini', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('feedsalidzini')
                    ->__('Edit XML File') : Mage::helper('feedsalidzini')->__('New XML File'),
                $id ? Mage::helper('feedsalidzini')
                    ->__('Edit XML File') : Mage::helper('feedsalidzini')->__('New XML File'))
            ->_addContent($this->getLayout()->createBlock('feedsalidzini/adminhtml_xmlgrid_edit'))
            ->renderLayout();
    }


    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $model = Mage::getModel('feedsalidzini/feedsalidzini');

            if ($this->getRequest()->getParam('xmlfile_id')) {
                $model ->load($this->getRequest()->getParam('xmlfile_id'));

                if ($model->getXmlfileFilename() && file_exists($model->getPreparedFilename())) {
                    unlink($model->getPreparedFilename());
                }
            }

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('feedsalidzini')->__('The XML file data has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('xmlfile_id' => $model->getId()));
                    return;
                }
                // go to grid or forward to generate action
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('xmlfile_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array(
                    'xmlfile_id' => $this->getRequest()->getParam('xmlfile_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('xmlfile_id')) {
            try {
                // init model and delete
                $model = Mage::getModel('feedsalidzini/feedsalidzini');
                $model->setId($id);
                // init and load  model
                $model->load($id);
                // delete file
                if ($model->getXmlfileFilename() && file_exists($model->getPreparedFilename())) {
                    unlink($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('feedsalidzini')->__('The Xml file has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('xmlfile_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('feedsalidzini')->__('Unable to find a Xml file to delete.'));
        // go to grid*/
        $this->_redirect('*/*/');
    }

    /**
     * Generate xml file
     */
    public function generateAction()
    {

        // init and load model
        $id      = $this->getRequest()->getParam('xmlfile_id');
        $xmlfile = Mage::getModel('feedsalidzini/feedsalidzini');
        $xmlfile->load($id);
        // if xml file record exists
        if ($xmlfile->getId()) {
            try {
                $xmlfile->generateXml();

                $this->_getSession()->addSuccess(
                    Mage::helper('feedsalidzini')->__(
                        'The xml file "%s" has been generated.',
                        $xmlfile->getXmlfileFilename()
                    )
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('feedsalidzini')->__('Unable to generate xml file.'));
            }
        } else {
            $this->_getSession()->addError(
                Mage::helper('feedsalidzini')->__('Unable to find a xml file to generate.'));
        }

        // go to grid
        $this->_redirect('*/*/');
    }


    /**
     * Check the permission to run it
     *
     * @return boolean
     */
     /*
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/sitemap');
    }
    */

}