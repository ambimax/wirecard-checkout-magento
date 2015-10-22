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

class Phoenix_WirecardCheckoutPage_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('wirecard_checkout_page/form.phtml');
    }

    protected function _getConfig()
    {
        return Mage::getSingleton('wirecard_checkout_page/config');
    }

    public function hasAdditionalForm()
    {
        return ($this->getAdditionalForm()) ? true : false;
    }

    public function getAdditionalForm()
    {
        $paymentType = strtoupper($this->getMethodCode());
        switch($paymentType)
        {
            case 'WIRECARD_CHECKOUT_PAGE_INVOICE':
                return 'wirecard_checkout_page/additional_Invoice';
                break;
            case 'WIRECARD_CHECKOUT_PAGE_INSTALLMENT':
                return 'wirecard_checkout_page/additional_Installment';
                break;
            case 'WIRECARD_CHECKOUT_PAGE_INVOICEB2B':
                return 'wirecard_checkout_page/additional_InvoiceB2b';
                break;
            default:
                return false;
                break;
        }
    }

    public function isSeamlessMode()
    {
        return Mage::getStoreConfigFlag('payment/' . $this->getMethodCode() . '/useSeamless', Mage::app()->getStore()->getId());
    }

    public function getSeamlessBlock($paymentType)
    {
        $paymentType = strtoupper(strval($paymentType));
        switch($paymentType)
        {
            case 'CC':
                return 'wirecard_checkout_page/seamless_Cc';
                break;
            case 'CCMOTO':
                return 'wirecard_checkout_page/seamless_CcMoto';
                break;
            case 'C2P':
                return 'wirecard_checkout_page/seamless_ClickTwoPay';
                break;
            case 'ELV':
                return 'wirecard_checkout_page/seamless_Elv';
                break;
            case 'SEPADD':
                return 'wirecard_checkout_page/seamless_SepaDd';
                break;
            case 'PBX':
                return 'wirecard_checkout_page/seamless_Pbx';
                break;
            case 'WGP':
                return 'wirecard_checkout_page/seamless_Wgp';
                break;
            case 'EPS':
                return 'wirecard_checkout_page/seamless_Eps';
                break;
            case 'IDL':
                return 'wirecard_checkout_page/seamless_Idl';
                break;
            default:
                return 'wirecard_checkout_page/seamless_Base';
        }
    }
}