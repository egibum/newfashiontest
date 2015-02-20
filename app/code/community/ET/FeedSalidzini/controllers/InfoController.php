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

class ET_FeedSalidzini_InfoController extends Mage_Core_Controller_Front_Action
{
    public function imageAction()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $width     = Mage::getStoreConfig('feedsalidzini/general/image_width');
        $height    = Mage::getStoreConfig('feedsalidzini/general/image_height');
        $watermark = Mage::getStoreConfig('design/watermark/list_small_image_image');
        $watermarkPosition = Mage::getStoreConfig('design/watermark/small_image_position');
        $productId = (int)Mage::app()->getRequest()->getParam('product');
        if ($productId && $width && $height) {
            $product = Mage::getModel('catalog/product')->load($productId);
            /*
            // Etot kod koroche i v 99% sluchaev podhodit.
            // No esli v magazine ispolzujutsa vodjanie znaki (watermarks),
            // to pri nekotorih razmerah izobrazhenij watermark promahivajetsa mimo kartinki
            // Poetomu bila ispolzovana model Mage::getModel('catalog/product_image')

            $url      = Mage::helper('catalog/image')->init($product, 'image')->resize($width, $height);
            $url      = strstr($url, "media/catalog");
            $type     = mime_content_type($url);
            $response = Mage::app()->getResponse();
            $response->setHeader("Content-Type", $type);
            */

            if ($product->getImage()) {
                $mediaBasePath = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
                if (file_exists( $mediaBasePath. $product->getImage())) {
                    $imageModel = Mage::getModel('catalog/product_image')->setDestinationSubdir($width.'x'.$height);

                    $imageModel->setBaseFile($product->getImage())
                        ->setWidth($width)->setHeight($height)
                        ->resize()
                        ->setWatermarkPosition($watermarkPosition)
                        ->setWatermark( $watermark )
                        ->saveFile();

                    $newImage = $imageModel->getNewFile();
                    $size = getimagesize($newImage);
                    $type = $size['mime'];
                    //$type     = mime_content_type($imageModel->getNewFile());
                    $response = Mage::app()->getResponse();
                    $response->setHeader("Content-Type", $type);
                    $response->setBody(file_get_contents($newImage));
                }
            }
        }
    }
}
