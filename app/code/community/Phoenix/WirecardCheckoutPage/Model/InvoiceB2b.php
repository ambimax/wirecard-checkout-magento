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

class Phoenix_WirecardCheckoutPage_Model_InvoiceB2b extends Phoenix_WirecardCheckoutPage_Model_Abstract
{
    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     **/
    protected $_code = 'wirecard_checkout_page_invoiceB2b';
    protected $_paymentMethod = 'INVOICE';

    /**
     * @param Mage_Sales_Model_Quote|null $quote
     * @return bool
     * @see Mage_Payment_Model_Method_Abstract::isAvailable()
     */
    public function isAvailable($quote = null)
    {
        //NOTE: NEVER return true in here. the parent check should do this!
        if($quote == null)
        {
            $quote = $this->_getQuote();
        }

        if($quote->hasVirtualItems())
        {
            return false;
        }

        $billingAddress = $quote->getBillingAddress();

        $shippingAddress = $quote->getShippingAddress();

        if(!$shippingAddress->getSameAsBilling())
        {
            if($billingAddress->getCustomerAddressId() == null || $billingAddress->getCustomerAddressId() != $shippingAddress->getCustomerAddressId())
            {
                if( //new line because it's easier to remove this way
                    $billingAddress->getName() != $shippingAddress->getName() ||
                    $billingAddress->getCompany() != $shippingAddress->getCompany() ||
                    $billingAddress->getCity() != $shippingAddress->getCity() ||
                    $billingAddress->getPostcode() != $shippingAddress->getPostcode() ||
                    $billingAddress->getCountryId() != $shippingAddress->getCountryId() ||
                    $billingAddress->getTelephone() != $shippingAddress->getTelephone() ||
                    $billingAddress->getFax() != $shippingAddress->getFax() ||
                    $billingAddress->getEmail() != $shippingAddress->getEmail() ||
                    $billingAddress->getCountry() != $shippingAddress->getCountry() ||
                    $billingAddress->getRegion() != $shippingAddress->getRegion() ||
                    $billingAddress->getStreet() != $shippingAddress->getStreet()
                )
                {
                    return false;
                }
            }
        }

        if($quote->getQuoteCurrencyCode() != 'EUR')
        {
            return false;
        }

        if (strlen($billingAddress->getCompany()))
            return true;

        $vat_id = $billingAddress->getData('vat_id');
        if (!strlen($vat_id))
            return false;

        return parent::isAvailable($quote);
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $result = parent::assignData($data);
        $key = 'wirecard_checkout_page_invoiceb2b_company_trade_reg_number';
        if (is_array($data)) {
            $this->getInfoInstance()->setAdditionalInformation($key, isset($data[$key]) ? $data[$key] : null);
        }
        elseif ($data instanceof Varien_Object) {
            $this->getInfoInstance()->setAdditionalInformation($key, $data->getData($key));
        }

        $this->getInfoInstance()->save();
        return $result;
    }

    /**
     * @return array|mixed|null|string
     */
    public function getCompanyTradeRegistrationNumber() {
        $additionalInfo = $this->getInfoInstance();
        $field = 'company_trade_reg_number';

        if($additionalInfo->hasAdditionalInformation('wirecard_checkout_page_invoiceb2b_company_trade_reg_number'))
        {
            $userCtrn = $additionalInfo->getAdditionalInformation('wirecard_checkout_page_invoiceb2b_company_trade_reg_number');
            if (Mage::getSingleton('customer/session')->getId() !== null) {
                $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
                $customer->setData($field, $userCtrn)->getResource()->saveAttribute($customer, $field);
            }
            return $userCtrn;
        }
        else
        {
            return "";
        }
    }

    protected function _getConsumerInformation()
    {
        $consumerInformation = parent::_getConsumerInformation();

        $billingAddress  = $this->getOrder()->getBillingAddress();
        if(empty($consumerInformation))
        {
            //if consumerInformation has been disabled we have to send at least mandatory invoice fields
            $consumerInformation['consumerBillingFirstname'] = $billingAddress->getFirstname();
            $consumerInformation['consumerBillingLastname']  = $billingAddress->getLastname();
            $consumerInformation['consumerBillingAddress1']  = $billingAddress->getStreet1();
            $consumerInformation['consumerBillingCity']      = $billingAddress->getCity();
            $consumerInformation['consumerBillingCountry']   = $billingAddress->getCountry();
            $consumerInformation['consumerBillingZipCode']   = $billingAddress->getPostcode();
            $consumerInformation['consumerEmail']            = $this->getOrder()->getCustomerEmail();
        }

        $consumerInformation['companyName']             = $billingAddress->getCompany();
        if (strlen($billingAddress->getData('vat_id')))
            $consumerInformation['companyVatId'] = $billingAddress->getData('vat_id');
        if (strlen($this->getCompanyTradeRegistrationNumber()))
            $consumerInformation['companyTradeRegistryNumber'] = $this->getCompanyTradeRegistrationNumber();

        if(isset($consumerInformation['consumerBirthDate'])) {
            unset($consumerInformation['consumerBirthDate']);
        }

        return $consumerInformation;
    }
}
