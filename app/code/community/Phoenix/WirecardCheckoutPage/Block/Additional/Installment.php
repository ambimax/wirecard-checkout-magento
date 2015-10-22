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

class Phoenix_WirecardCheckoutPage_Block_Additional_Installment extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('wirecard_checkout_page/additional/installment.phtml');
    }

    private function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    private function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function getCustomerDob() {
        $quote = $this->getQuote();
        return $quote->getCustomerDob();
    }

    private function getCustomerDobPart($mask) {
        $dob = $this->getCustomerDob();
        if($dob) {
            return Mage::app()->getLocale()->date($dob, null, null, false)->toString($mask);
        }
        return '';
    }

    public function getCustomerDobYear() {
        return $this->getCustomerDobPart('yyyy');
    }

    public function getCustomerDobMonth() {
        return $this->getCustomerDobPart('MM');
    }

    public function getCustomerDobDay() {
        return $this->getCustomerDobPart('dd');
    }
}