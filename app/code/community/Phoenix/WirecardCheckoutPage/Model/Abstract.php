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
 *c
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

abstract class Phoenix_WirecardCheckoutPage_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    **/
    protected $_code = 'wirecard_checkout_page_abstract';

    protected $_isGateway                = false;
    protected $_canAuthorize            = true;
    protected $_canCapture                = true;
    protected $_canCapturePartial        = false;
    protected $_canRefund                = false;
    protected $_canVoid                    = false;
    protected $_canUseInternal            = false;
    protected $_canUseCheckout            = true;
    protected $_canUseForMultishipping    = false;

    protected $_paymentMethod            = 'SELECT';
    protected $_defaultLocale            = 'en';

    protected $_order;
    protected $_pluginVersion        = '3.6.4';
    protected $_pluginName           = 'Phoenix/WirecardCheckoutPage';

    protected $_formBlockType = 'wirecard_checkout_page/form';
    protected $_infoBlockType = 'wirecard_checkout_page/info';

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = false;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order)
        {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }

    public function getOrderPlaceRedirectUrl()
    {
        if ($this->isSeamlessMode() && Mage::helper('wirecard_checkout_page')->isDataStorageEnabled()) {
            if (Mage::getStoreConfigFlag('payment/'.$this->_code.'/useIFrame', Mage::app()->getStore()->getId())) {
                $redirectUrl = Mage::getUrl('wirecard_checkout_page/processing/wirecard_checkout_pagecheckout', array('_secure' => true));

            } else {
                $redirectUrl = Mage::getSingleton('core/session')->getWirecardCheckoutPageRedirectUrl();
                Mage::getSingleton('core/session')->unsWirecardCheckoutPageRedirectUrl();
            }
            return $redirectUrl;
        }

        Mage::getSingleton('core/session')->unsWirecardCheckoutPageRedirectUrl();
        return Mage::getUrl('wirecard_checkout_page/processing/wirecard_checkout_pagecheckout', array('_secure' => true));
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());
        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * parse response POST array from gateway page and return payment status
     *
     * @return bool
     */
    public function parseResponse()
    {
        $response = $this->getResponse();
        if (!empty($response['paymentState']) &&
            !empty($response['orderNumber']) &&
            !empty($response['paymentType']) &&
            !empty($response['responseFingerprintOrder']) &&
            !empty($response['responseFingerprint']))
        {
            $fp_array = explode(',', $response['responseFingerprintOrder']);

            if (!in_array('secret', $fp_array))
                return false;

            $fp_string = '';
            foreach ($fp_array as $val)
            {
                if ($val == 'secret')
                {
                    $fp_string .= $this->getConfigData('secret_key');
                    continue;
                }
                $fp_string .= $response[$val];
            }
            if (md5($fp_string) == $response['responseFingerprint'])
                return true;
        }
        return false;
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }

    public function getUrl()
    {
        $session = Mage::getSingleton('core/session');

        if (!$session->getWirecardCheckoutPageRedirectUrl())
        {
            $params = $this->getFormFields();
            $client = new Zend_Http_Client();
            $response = $client->setUri(Phoenix_WirecardCheckoutPage_Helper_Configuration::getWcpFrontendUrl() . '/init-server.php')
                               ->setMethod(Zend_Http_Client::POST)
                               ->setParameterPost($params)
                               ->request();
            $responseKeyValuePairs = explode('&', $response->getBody());
            $response = Array();
            foreach($responseKeyValuePairs AS $responseKeyValuePair)
            {
                $responseEntry = explode('=', $responseKeyValuePair);
                $response[urldecode($responseEntry[0])] = urldecode($responseEntry[1]);
            }

            if(!isset($response['redirectUrl']))
            {
                if(isset($response['consumerMessage']))
                {
                    $message = $response['consumerMessage'];
                }
                else
                {
                    $message = Mage::helper('wirecard_checkout_page')->__('An error during the payment process occurred.');
                }
                if(isset($response['message']))
                {
                    Mage::log($this->_wirecardCheckoutPageInitUrl . ' request failed: ' . $response['message']);
                }
                else
                {
                    Mage::log($this->_wirecardCheckoutPageInitUrl . ': No valid Wirecard Checkout Page response.');
                }
                throw new Exception($message);
            }
            else
            {
                $session->setWirecardCheckoutPageRedirectUrl($response['redirectUrl']);
            }
        }
        return $session->getWirecardCheckoutPageRedirectUrl();
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
        $currency        = $this->getOrder()->getBaseCurrencyCode();
        $returnurl       = Mage::getUrl('wirecard_checkout_page/processing/checkresponse', array('_secure'=>true, '_nosid'=>true));
        $shopName        = 'Magento';
        $shopVersion     = Mage::getVersion();
        $pluginName      = $this->getPluginName();
        $pluginVersion   = $this->_pluginVersion;
        $versionString   = base64_encode($shopName.';'.$shopVersion.'; ;'.$pluginName.';'.$pluginVersion);
        $deliveryAddress = $this->getOrder()->getShippingAddress();
        $billingAddress  = $this->getOrder()->getBillingAddress();

         $locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
        if (is_array($locale) && !empty($locale))
            $locale = $locale[0];
        else
            $locale = $this->getDefaultLocale();

        $params =     array(
                        'customerId'            => $this->getConfigData('customer_id'),
                        'amount'                => round($this->getOrder()->getBaseGrandTotal(), 2),
                        'currency'              => $currency,
                        'language'              => $locale,
                        'orderDescription'      => $this->getOrder()->getRealOrderId(),
                        'customerStatement'     => $this->getOrder()->getRealOrderId(),
                        'successURL'            => $returnurl,
                        'cancelURL'             => $returnurl,
                        'failureURL'            => $returnurl,
                        'pendingURL'            => $returnurl,
                        'serviceURL'            => Mage::getUrl($this->getConfigData('service_url'), array('_nosid'=>true)),
                        'confirmURL'            => Mage::getUrl('wirecard_checkout_page/processing/confirm', array('_secure'=>true, '_nosid'=>true)),
                        'duplicateRequestCheck' => 'no',
                        'paymenttype'           => $this->_paymentMethod,
                        'orderId'               => $this->getOrder()->getRealOrderId(),
                        'orderReference'        => $this->getOrder()->getRealOrderId(),
                        'pluginVersion'         => $versionString,
                        'backgroundColor'       => $this->getConfigData('background_color'),
                        'consumerUserAgent'     => Mage::app()->getRequest()->getHeader('User-Agent'),
                        'consumerIpAddress'     => Mage::app()->getRequest()->getServer('REMOTE_ADDR')
                    );

        $params = array_merge($params, $this->_getConsumerInformation());

        if (strlen($this->getConfigData('shop_id')) > 0)
            $params['shopId'] = $this->getConfigData('shop_id');
        if (strlen($this->getConfigData('display_text')) > 0)
            $params['displayText'] = $this->getConfigData('display_text');
        if (strlen($this->getConfigData('logo_url')) > 0)
            $params['imageURL'] = Mage::getDesign()->getSkinUrl($this->getConfigData('logo_url'));

        if($this->getConfigData('auto_deposit'))
        {
            $params['autoDeposit'] = 'yes';
        }

        if($this->getConfigData('detect_layout'))
        {
            $detect = new WirecardCEE_MobileDetect();

            if($detect->isTablet()) {
                $layout = 'TABLET';
            }
            elseif($detect->isMobile()) {
                $layout = 'SMARTPHONE';
            }
            else {
                $layout = 'DESKTOP';
            }

            $params['layout'] = $layout;
        }

        // compile fingerprint
        $requestFingerprintOrder = 'secret,';
        $requestFingerprintSeed  = $this->getConfigData('secret_key');
        foreach($params as $key => $value)
        {
            if($value == NULL)
            {
                unset($params[$key]);
            }
            else
            {
                $requestFingerprintOrder .= $key.',';
                $requestFingerprintSeed .= $value;
            }
        }
        $requestFingerprintOrder .= 'requestFingerprintOrder';
        $requestFingerprintSeed .= $requestFingerprintOrder;
        $requestfingerprint = md5($requestFingerprintSeed);
        $params['requestFingerprintOrder'] = $requestFingerprintOrder;
        $params['requestFingerprint'] = $requestfingerprint;
        return $params;
    }

    protected function _getConsumerInformation()
    {
        $consumerInformation = Array();
        if($this->getConfigData('send_additional_data'))
        {
            $deliveryAddress = $this->getOrder()->getShippingAddress();
            $billingAddress  = $this->getOrder()->getBillingAddress();
            $dob = new DateTime($this->getOrder()->getCustomerDob());

            $consumerInformation['consumerBillingFirstName'] = $billingAddress->getFirstname();
            $consumerInformation['consumerBillingLastName'] = $billingAddress->getLastname();
            $consumerInformation['consumerBillingAddress1'] = $billingAddress->getStreet1();
            $consumerInformation['consumerBillingAddress2'] = $billingAddress->getStreet2();
            $consumerInformation['consumerBillingCity'] = $billingAddress->getCity();
            $consumerInformation['consumerBillingCountry'] = $billingAddress->getCountry();
            $consumerInformation['consumerBillingState'] = $billingAddress->getRegionCode();
            $consumerInformation['consumerBillingZipCode'] = $billingAddress->getPostcode();
            $consumerInformation['consumerBillingPhone'] = $billingAddress->getTelephone();
            $consumerInformation['consumerBillingFax'] = $billingAddress->getFax();
            $consumerInformation['consumerEmail'] = $this->getOrder()->getCustomerEmail();
            $consumerInformation['consumerBirthDate'] = $dob->format('Y-m-d');

            if($deliveryAddress)
            {
                $consumerInformation['consumerShippingFirstName'] = $deliveryAddress->getFirstname();
                $consumerInformation['consumerShippingLastName'] = $deliveryAddress->getLastname();
                $consumerInformation['consumerShippingStreet1'] = $deliveryAddress->getStreet1();
                $consumerInformation['consumerShippingStreet2'] = $deliveryAddress->getStreet2();
                $consumerInformation['consumerShippingCity'] = $deliveryAddress->getCity();
                $consumerInformation['consumerShippingCountry'] = $deliveryAddress->getCountry();
                $consumerInformation['consumerShippingState'] = $deliveryAddress->getRegionCode();
                $consumerInformation['consumerShippingZipCode'] = $deliveryAddress->getPostcode();
                $consumerInformation['consumerShippingPhone'] = $deliveryAddress->getTelephone();
                $consumerInformation['consumerShippingFax'] = $deliveryAddress->getFax();
            }
        }
        return $consumerInformation;
    }

    /**
     * Check if the payment Method is set to seamless mode
     *
     * @return boolean
     */
    public function isSeamlessMode()
    {
        return Mage::getStoreConfigFlag('payment/' . $this->_code . '/useSeamless', Mage::app()->getStore()->getId());
    }

    /**
     *
     * Getter for the plugin version variable
     *
     * @return string  The plugin version
     */
    public function getPluginVersion()
    {
        return $this->_pluginVersion;
    }

    /**
     *
     * Getter for the plugin name variable
     *
     * @return string  The plugin name
     */
    public function getPluginName()
    {
        return $this->_pluginName;
    }

    public function getFinancialInstitution()
    {
        return null;
    }

    /**
     * getter for customers birthDate
     * @return DateTime|boolean
     */
    public function getCustomerDob()
    {
        $order = $this->getOrder();
        $dob = $order->getCustomerDob();
        if($dob)
        {
            return new DateTime($dob);
        }
        return false;
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    protected function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }
}
