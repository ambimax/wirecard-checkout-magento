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

class Phoenix_WirecardCheckoutPage_Model_Observer
    extends Varien_Object
{
    /**
     * The given Order Object from Observer
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Process the seamless Payment after Order is complete
     *
     * @param Varien_Event_Observer $observer
     *
     * @throws Exception
     * @return Phoenix_WirecardCheckoutPage_Model_Observer
     */
    public function salesOrderPaymentPlaceEnd(Varien_Event_Observer $observer)
    {
        /**
         * @var Phoenix_WirecardCheckoutPage_Model_Abstract
         */
        $payment         = $observer->getPayment();
        $this->_order    = $payment->getOrder();
        $storeId         = $this->_order->getStoreId();
        $paymentInstance = $payment->getMethodInstance();

        if (Mage::getStoreConfigFlag('payment/'.$payment->getMethod().'/useSeamless', $storeId)) {

            $storageId     = $payment->getAdditionalData();
            $orderIdent    = $this->_order->getQuoteId();
            $customerId    = Mage::getStoreConfig('payment/'.$payment->getMethod().'/customer_id', $storeId);
            $shopId        = Mage::getStoreConfig('payment/'.$payment->getMethod().'/shop_id', $storeId);
            $secretKey     = Mage::getStoreConfig('payment/'.$payment->getMethod().'/secret_key', $storeId);
            $serviceUrl    = Mage::getUrl(Mage::getStoreConfig('payment/'.$payment->getMethod().'/service_url', $storeId));
            $paymentType   = $this->_getMappedPaymentCode($payment->getMethod());
            $returnurl     = Mage::getUrl('wirecard_checkout_page/processing/checkresponse', array('_secure'=>true, '_nosid'=>true));
            $pluginVersion = WirecardCEE_Client_QPay_Request_Initiation::generatePluginVersion('Magento', Mage::getVersion(), $paymentInstance->getPluginName(), $paymentInstance->getPluginVersion());
            $initiation    = new WirecardCEE_Client_QPay_Request_Initiation($customerId, $shopId, $secretKey, substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2), $pluginVersion);
            $consumerData  = new WirecardCEE_Client_QPay_Request_Initiation_ConsumerData();

            if(Mage::getStoreConfigFlag('payment/'.$payment->getMethod().'/send_additional_data', $storeId))
            {
                $consumerData->setEmail($this->_order->getCustomerEmail());
                $dob = $payment->getMethodInstance()->getCustomerDob();
                if($dob)
                {
                    $consumerData->setBirthDate($dob);
                }
                $consumerData->addAddressInformation($this->_getBillingObject());

                if($this->_order->hasShipments())
                {
                    $consumerData->addAddressInformation($this->_getShippingObject());
                }
            }
            if($payment->getMethod() == 'wirecard_checkout_page_invoice' || $payment->getMethod() == 'wirecard_checkout_page_installment')
            {
                $consumerData->setEmail($this->_order->getCustomerEmail());
                $dob = $payment->getMethodInstance()->getCustomerDob();
                if($dob)
                {
                    $consumerData->setBirthDate($dob);
                }
                else
                {
                    throw new Exception('Invalid dob');
                }
                $consumerData->addAddressInformation($this->_getBillingObject('invoice'));
            }
            $consumerData->setIpAddress($this->_order->getRemoteIp());
            $consumerData->setUserAgent(Mage::app()->getRequest()->getServer('HTTP_USER_AGENT'));

            $initiation->setConfirmUrl(Mage::getUrl('wirecard_checkout_page/processing/seamlessConfirm', array('_secure'=>true, '_nosid'=>true)));
            $initiation->setWindowName('paymentIframe');

            $initiation->orderId = $this->_order->getIncrementId();
            $initiation->companyTradeRegistryNumber = $payment->getMethodInstance()->getCompanyTradeRegistrationNumber();

            if($orderIdent && $storageId) {
                $initiation->setStorageReference($orderIdent, $storageId);
            }

            if(Mage::getStoreConfigFlag('payment/'.$payment->getMethod().'/auto_deposit', $storeId))
            {
                $initiation->setAutoDeposit(true);
            }

            $initiation->setOrderReference($this->_order->getIncrementId());

            $financialInstitution = $payment->getMethodInstance()->getFinancialInstitution();
            if($financialInstitution)
            {
                $initiation->setFinancialInstitution($financialInstitution);
            }

            Phoenix_WirecardCheckoutPage_Helper_Configuration::configureWcsLibrary();
            $response = $initiation->initiate(round($this->_order->getBaseGrandTotal(), 2),
                                              $this->_order->getBaseCurrencyCode(),
                                              $paymentType,
                                              $this->_order->getIncrementId(),
                                              $returnurl,
                                              $returnurl,
                                              $returnurl,
                                              $returnurl,
                                              $serviceUrl,
                                              $consumerData
                                              );

            if(isset($response) && $response->getStatus() == WirecardCEE_Client_QPay_Response_Initiation::STATE_SUCCESS) {
               $payment->setAdditionalData(serialize($payment->getAdditionalData()))->save();
               Mage::getSingleton('core/session')->unsetData('wirecard_checkout_page_payment_info');
               Mage::getSingleton('core/session')->setWirecardCheckoutPageRedirectUrl(urldecode($response->getRedirectUrl()));

            } elseif (isset($response)) {

                $errorMessage = '';
                foreach ($response->getErrors() as $error) {
                    $errorMessage .= ' ' . $error->getMessage();
                }
                throw new Exception(trim($errorMessage));

            } else {
                $payment->setAdditionalData(serialize($payment->getAdditionalData()))->save();
                Mage::getSingleton('core/session')->unsetData('wirecard_checkout_page_payment_info');
            }
        }

        return $this;
    }

    /**
     * Generate and return the Wirecard billing Object
     *
     * @param bool $specificPaymentType
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    protected function _getBillingObject($specificPaymentType = false)
    {
        $billing           = $this->_order->getBillingAddress();
        $billingAddressObj = new WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address(WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address::TYPE_BILLING);

        $billingAddressObj->setFirstname($billing->getFirstname());
        $billingAddressObj->setLastname($billing->getLastname());
        $billingAddressObj->setAddress1($billing->getStreet1());
        $billingAddressObj->setCity($billing->getCity());
        $billingAddressObj->setCountry($billing->getCountry());
        $billingAddressObj->setZipCode($billing->getPostcode());
        if($specificPaymentType != 'invoice' && $specificPaymentType != 'installment')
        {
            $billingAddressObj->setAddress2($billing->getStreet2());
            $billingAddressObj->setState($billing->getRegionCode());
            $billingAddressObj->setFax($billing->getFax());
            $billingAddressObj->setPhone($billing->getTelephone());
        }


        return $billingAddressObj;
    }

    /**
     * Generate and return the Wirecard shipping Object if shipping is necessary
     *
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    protected function _getShippingObject()
    {
        $shipping           = $this->_order->getShippingAddress();
        $shippingAddressObj = new WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address(WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address::TYPE_SHIPPING);

        $shippingAddressObj->setFirstname($shipping->getFirstname());
        $shippingAddressObj->setLastname($shipping->getLastname());
        $shippingAddressObj->setAddress1($shipping->getStreet1());
        $shippingAddressObj->setAddress2($shipping->getStreet2());
        $shippingAddressObj->setCity($shipping->getCity());
        $shippingAddressObj->setCountry($shipping->getCountry());
        $shippingAddressObj->setState($shipping->getRegionCode());
        $shippingAddressObj->setZipCode($shipping->getPostcode());
        $shippingAddressObj->setFax($shipping->getFax());
        $shippingAddressObj->setPhone($shipping->getTelephone());

        return $shippingAddressObj;
    }

    /**
     * Map the Magento Payment Code with the Wirecard Payment Code
     *
     * @param string $paymentCode The Magento Payment Code
     *
     * @return string
     */
    protected function _getMappedPaymentCode($paymentCode)
    {
        switch ($paymentCode)
        {
            case 'wirecard_checkout_page_select':
                return 'SELECT';
                break;
            case 'wirecard_checkout_page_banContactMisterCash':
                return 'BANCONTACT_MISTERCASH';
                break;
            case 'wirecard_checkout_page_cc':
                return 'CCARD';
                break;
            case 'wirecard_checkout_page_c2p':
                return 'C2P';
                break;
            case 'wirecard_checkout_page_elv':
                return 'ELV';
                break;
            case 'wirecard_checkout_page_sepadd':
                return 'SEPA-DD';
                break;
            case 'wirecard_checkout_page_eps':
                return 'EPS';
                break;
            case 'wirecard_checkout_page_idl':
                return 'IDL';
                break;
            case 'wirecard_checkout_page_invoice':
            case 'wirecard_checkout_page_invoiceB2b':
                return 'INVOICE';
                break;
            case 'wirecard_checkout_page_installment':
                return 'INSTALLMENT';
                break;
            case 'wirecard_checkout_page_maestro':
                return 'MAESTRO';
                break;
            case 'wirecard_checkout_page_moneta':
                return 'MONETA';
                break;
            case 'wirecard_checkout_page_paypal':
                return 'PAYPAL';
                break;
            case 'wirecard_checkout_page_pbx':
                return 'PBX';
                break;
            case 'wirecard_checkout_page_poli':
                return 'POLI';
                break;
            case 'wirecard_checkout_page_przelewy':
                return 'PRZELEWY24';
                break;
            case 'wirecard_checkout_page_psc':
                return 'PSC';
                break;
            case 'wirecard_checkout_page_quick':
                return 'QUICK';
                break;
            case 'wirecard_checkout_page_sofortueberweisung':
                return 'sofortueberweisung';
                break;
            case 'wirecard_checkout_page_wgp':
                return 'GIROPAY';
                break;
            case 'wirecard_checkout_page_instantBank':
                return 'TRUSTLY';
                break;
            case 'wirecard_checkout_page_eKonto':
                return 'EKONTO';
                break;
            case 'wirecard_checkout_page_ccMoto':
                return 'CCARD-MOTO';
                break;
            case 'wirecard_checkout_page_mpass':
                return 'MPASS';
                break;
            case 'wirecard_checkout_page_skrilldirect':
                return 'SKRILLDIRECT';
                break;
            case 'wirecard_checkout_page_skrillwallet':
                return 'SKRILLWALLET';
                break;
            case 'wirecard_checkout_page_tatrapay':
                return 'TATRAPAY';
                break;
            case 'wirecard_checkout_page_epay_bg':
                return 'EPAY_BG';
                break;
            default:
                return '';
        }
    }
}