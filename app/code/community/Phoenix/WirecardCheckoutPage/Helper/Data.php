<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * Shop System Plugins - Terms of use
 *
 * This terms of use regulates warranty and liability between
 * Wirecard Central Eastern Europe (subsequently referred to as WDCEE)
 * and it's contractual partners (subsequently referred to as customer or customers)
 * which are related to the use of plugins provided by WDCEE.
 * The Plugin is provided by WDCEE free of charge for it's customers and
 * must be used for the purpose of WDCEE's payment platform integration only.
 * It explicitly is not part of the general contract between WDCEE and it's customer.
 * The plugin has successfully been tested under specific circumstances
 * which are defined as the shopsystem's standard configuration (vendor's delivery state).
 * The Customer is responsible for testing the plugin's functionality
 * before putting it into production enviroment.
 * The customer uses the plugin at own risk. WDCEE does not guarantee it's full
 * functionality neither does WDCEE assume liability for any disadvantage related
 * to the use of this plugin. By installing the plugin into the shopsystem the customer
 * agrees to the terms of use. Please do not use this plugin if you do not agree to the terms of use!
 *
 * @category   Phoenix
 * @package    Phoenix_WirecardCheckoutPage
 * @copyright  Copyright (c) 2008 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 */
class Phoenix_WirecardCheckoutPage_Helper_Data extends Mage_Payment_Helper_Data
{
    /**
     * Check if the Semaless Data Storage is enabled
     *
     * @return boolean
     */
    public function isDataStorageEnabled()
    {
        if (Mage::getSingleton('checkout/session')->getData('wirecard_checkout_page_data_storage_enabled')) {
            return true;
        }
        return false;
    }

    /**
     * @param $customerId
     * @param $storeId
     * @param $shopId
     * @param $methodCode
     * @return array|bool
     */
    public function startWirecardCEE($customerId, $storeId, $shopId, $methodCode)
    {
        Phoenix_WirecardCheckoutPage_Helper_Configuration::configureWcsLibrary();
        $secretKey = Mage::getStoreConfig('payment/' . $methodCode . '/secret_key', $storeId);
        $returnUrl = Mage::getUrl('wirecard_checkout_page/processing/storereturn', array('_secure' => true));
        $quoteId = Mage::getSingleton('checkout/session')->getQuote()->getId();
        $language = substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);

        $dataStorageInit = new WirecardCEE_Client_DataStorage_Request_Initiation(
            $customerId,
            $shopId,
            $language,
            $returnUrl,
            $secretKey
        );

        // check for pci option in any of the creditcard paymenttypes

        $pci3 = false;
        if ((bool)Mage::getStoreConfigFlag('payment/wirecard_checkout_page_cc/active') &&
            (bool)Mage::getStoreConfigFlag('payment/wirecard_checkout_page_cc/pci3DssSaqAEnable')
        ) {
            $pci3 = true;
        }

        if ((bool)Mage::getStoreConfigFlag('payment/wirecard_checkout_page_ccMoto/active') &&
            (bool)Mage::getStoreConfigFlag('payment/wirecard_checkout_page_ccMoto/pci3DssSaqAEnable')
        ) {
            $pci3 = true;
        }

        if ($pci3) {
            $showIssueDate = (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_cc/show_issue_date',
                    Mage::app()->getStore()->getId()
                ) ||
                (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_ccMoto/show_issue_date',
                    Mage::app()->getStore()->getId()
                );
            $showIssueNumber = (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_cc/show_issue_number',
                    Mage::app()->getStore()->getId()
                ) ||
                (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_ccMoto/show_issue_number',
                    Mage::app()->getStore()->getId()
                );
            $showCardholder = (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_cc/show_cardholder',
                    Mage::app()->getStore()->getId()
                ) ||
                (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_ccMoto/show_cardholder',
                    Mage::app()->getStore()->getId()
                );
            $showCvc = (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_cc/show_cvc',
                    Mage::app()->getStore()->getId()
                ) ||
                (bool)Mage::getStoreConfigFlag(
                    'payment/wirecard_checkout_page_ccMoto/show_cvc',
                    Mage::app()->getStore()->getId()
                );
            $iFrameCss = strlen(
                Mage::getStoreConfig('payment/wirecard_checkout_page_cc/iframeCss', Mage::app()->getStore()->getId())
            ) ? Mage::getStoreConfig(
                'payment/wirecard_checkout_page_cc/iframeCss',
                Mage::app()->getStore()->getId()
            ) : Mage::getStoreConfig('payment/wirecard_checkout_page_cc/iframeCss', Mage::app()->getStore()->getId());

            $dataStorageInit->setCreditCardShowIssueDateField($showIssueDate);
            $dataStorageInit->setCreditCardShowIssueNumberField($showIssueNumber);
            $dataStorageInit->setCreditCardCardholderNameField($showCardholder);
            $dataStorageInit->setCreditCardShowCvcField($showCvc);
            $dataStorageInit->setJavascriptScriptVersion('pci3');
            $dataStorageInit->setIframeCssUrl($iFrameCss);
        }

        $storageId = '';
        try {

            $response = $dataStorageInit->initiate($quoteId);
Mage::log($response);
            if ($response->getStatus() == WirecardCEE_Client_DataStorage_Response_Initiation::STATE_SUCCESS) {

                $storageId = $response->getStorageId();
                $javascriptUrl = $response->getJavascriptUrl();

                Mage::getSingleton('checkout/session')->setData('wirecard_checkout_page_data_storage_enabled', '1');

                return array('storageId' => $storageId, 'javascriptUrl' => $javascriptUrl);

            } else {

                Mage::getSingleton('checkout/session')->setData('wirecard_checkout_page_data_storage_enabled', false);
                $dsErrors = $response->getErrors();

                foreach ($dsErrors as $error) {
                    Mage::log($error->getMessage(), true, 'wirecard_checkout_page_exception.log');
                }
                return false;
            }
        } catch (WirecardCEE_Exception $e) {

            //communication with dataStorage failed. we choose a none dataStorage fallback
            Mage::getSingleton('checkout/session')->setData('wirecard_checkout_page_data_storage_enabled', false);
            Mage::logException($e);
            return false;
        }
    }

    /**
     * @param $customerId
     * @param $storeId
     * @param $shopId
     * @param $methodCode
     * @return WirecardCEE_Client_DataStorage_Response_Read|bool
     */
    public function readWirecardCEE($customerId, $storeId, $shopId, $methodCode)
    {
        Phoenix_WirecardCheckoutPage_Helper_Configuration::configureWcsLibrary();
        $secretKey = Mage::getStoreConfig('payment/' . $methodCode . '/secret_key', $storeId);

        $session = Mage::getSingleton('checkout/session');

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $payment = $order->getPayment();

        $storageId = $payment->getAdditionalData();

        $dataStorageRead = new WirecardCEE_Client_DataStorage_Request_Read($customerId, $shopId, $secretKey);

        try {

            $response = $dataStorageRead->read($storageId);

            if ($response->getStatus() != WirecardCEE_Client_DataStorage_Response_Read::STATE_FAILURE) {

                return $response;

            } else {

                Mage::getSingleton('checkout/session')->setData('wirecard_checkout_page_data_storage_enabled', false);
                $dsErrors = $response->getErrors();

                foreach ($dsErrors as $error) {
                    Mage::log($error->getMessage(), true, 'wirecard_checkout_page_exception.log');
                }
                return false;
            }
        } catch (WirecardCEE_Exception $e) {

            //communication with dataStorage failed. we choose a none dataStorage fallback
            Mage::getSingleton('checkout/session')->setData('wirecard_checkout_page_data_storage_enabled', false);
            Mage::logException($e);
            return false;
        }
    }
}
