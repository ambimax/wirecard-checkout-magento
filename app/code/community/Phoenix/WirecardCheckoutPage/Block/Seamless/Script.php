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

class Phoenix_WirecardCheckoutPage_Block_Seamless_Script extends Mage_Core_Block_Template
{
    protected $_dataStorageUrl;

    /**
     * Internal constructor, that is called from real constructor
     */
    protected function _construct()
    {
        $this->_setMethodCode();
    }

    public function _beforeToHtml()
    {
        if ($this->hasData('method_code')) {
            return parent::_beforeToHtml();
        }
        else
        {
            return false;
        }
    }

    /**
     * Set the first active Payment Method Code to the Object.
     * We take the config from this Payment Method.
     */
    protected function _setMethodCode()
    {
        if ($this->_isMethodEnabled('wirecard_checkout_page_cc'))
        {
            $this->setData('method_code', 'wirecard_checkout_page_cc');
        } elseif ($this->_isMethodEnabled('wirecard_checkout_page_ccMoto')) {
            $this->setData('method_code', 'wirecard_checkout_page_ccMoto');
        } elseif ($this->_isMethodEnabled('wirecard_checkout_page_elv')) {
            $this->setData('method_code', 'wirecard_checkout_page_elv');
        } elseif ($this->_isMethodEnabled('wirecard_checkout_page_sepadd')) {
            $this->setData('method_code', 'wirecard_checkout_page_sepadd');
        } elseif ($this->_isMethodEnabled('wirecard_checkout_page_pbx')) {
            $this->setData('method_code', 'wirecard_checkout_page_pbx');
        } elseif ($this->_isMethodEnabled('wirecard_checkout_page_wgp')) {
            $this->setData('method_code', 'wirecard_checkout_page_wgp');
        }
    }

    /**
     * Check if the given Payment Method is enabled
     *
     * @param string $code
     * @return bool
     */
    protected function _isMethodEnabled($code)
    {
        $storeId = Mage::app()->getStore()->getId();

        if (Mage::getStoreConfigFlag('payment/'.$code.'/active', $storeId)
            && Mage::getStoreConfigFlag('payment/'.$code.'/useSeamless', $storeId)) {
                return true;
            }

        return false;
    }

    public function getDataStorageUrl()
    {
        if(!$this->_dataStorageUrl)
        {
            $storeId    = Mage::app()->getStore()->getId();
            $shopId     = Mage::getStoreConfig('payment/'.$this->getMethodCode().'/shop_id', $storeId);
            $customerId = Mage::getStoreConfig('payment/'.$this->getMethodCode().'/customer_id', $storeId);
            $dsVersion  = Mage::getStoreConfig('payment/'.$this->getMethodCode().'/data_storage', $storeId);

            /** @var Phoenix_WirecardCheckoutPage_Helper_Data $helper */
            $helper = Mage::helper('wirecard_checkout_page');
            $storageResponse = $helper->startWirecardCEE($customerId, $storeId, $shopId, $this->getMethodCode());

            Mage::getSingleton('core/session')->setStorageId($storageResponse['storageId']);
            if ($storageResponse)
            {
                $this->_dataStorageUrl = $storageResponse['javascriptUrl'];
            }
            else
            {
                return false;
            }
        }
        return $this->_dataStorageUrl;
    }

}