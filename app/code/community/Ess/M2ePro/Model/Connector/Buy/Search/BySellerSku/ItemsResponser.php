<?php

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Buy_Search_BySellerSku_ItemsResponser
    extends Ess_M2ePro_Model_Connector_Buy_Responser
{
    // ########################################

    protected function validateResponseData($response)
    {
        if (!isset($response['items'])) {
            return false;
        }

        return true;
    }

    protected function processResponseData($response)
    {
        $products = array();

        foreach ($response['items'] as $item) {

            if (isset($item['variations'])) {
                $product = array(
                    'title' => $item['title'],
                    'image_url' => $item['image_url'],
                    'variations' => $item['variations']
                );

                $products[] = $product;
                continue;
            }

            $product = array(
                'general_id' => $item['product_id'],
                'title' => $item['title'],
                'image_url' => $item['image_url']
            );

            if (!empty($item['price'])) {
                $product['price'] = $item['price'];
            }

            $products[] = $product;
        }

        $this->processParsedResult($products);
    }

    // ########################################

    abstract protected function processParsedResult($result);

    // ########################################
}