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

class Phoenix_WirecardCheckoutPage_ProcessingController extends Mage_Core_Controller_Front_Action
{
    protected $paymentInst;

    /** @var  Mage_Sales_Model_Order */
    protected $order;

    public function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems())
        {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

  /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

  /**
     * checkout Page for IFrame include
     */
    public function wirecard_checkout_pageCheckoutAction()
    {
        $session = $this->getCheckout();

        /**
         * @var Mage_Model_Sales_Order
         */
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        if(!$this->_succeeded($order))
        {
            $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('wirecard_checkout_page')->__('Customer was redirected to Wirecard Checkout Page.'))->save();
            // Save quote id in session and clean it
            $session->setWirecardCheckoutPageQuoteId($session->getQuoteId());
            $session->getQuote()->setIsActive(false)->save();
            $session->clear();

            $paymentInst = $order->getPayment()->getMethodInstance();
            $detectLayout = new WirecardCEE_MobileDetect();

            if(($paymentInst->getConfigData('useIFrame') && !$session->getIsQMoreIframe()) &&
                ($paymentInst->getConfigData('detect_layout') == false || ($paymentInst->getConfigData('detect_layout') == true && $detectLayout->isMobile() == false)))
            {
                $this->loadLayout();
                $this->renderLayout();
            }
            else
            {
                $session->unsIsQMoreIframe();
                try
                {
                    $this->_redirectUrl($paymentInst->getUrl());
                }
                catch(Exception $e)
                {
                    if($paymentInst->getConfigData('useSeamless'))
                    {
                        throw $e;
                    }
                    else
                    {
                        $quoteId = $this->getCheckout()->getLastQuoteId();
                        if ($quoteId) {
                            $quote = Mage::getModel('sales/quote')->load($quoteId);
                            if ($quote->getId()) {
                                $quote->setIsActive(true)->save();
                                $this->getCheckout()->setQuoteId($quoteId);
                            }
                        }
                        $this->getCheckout()->addNotice($e->getMessage());
                        $this->_redirectUrl(Mage::getUrl('checkout/cart/'));
                    }
                }
                return;
            }
        }
        else
        {
            $this->norouteAction();
        }
    }

    public function isIframeAction()
    {
        $result = Array();
        $paymentMethod = $this->getRequest()->getParam('paymentMethod', null);
        if(!$paymentMethod)
        {
            $result['isIframe'] = false;
        }
        else if(preg_match('/^wirecard_checkout_page/', $paymentMethod))
        {
            $session = $this->getCheckout();
            $detectLayout = new WirecardCEE_MobileDetect();

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            $paymentInst = $order->getPayment()->getMethodInstance();

            if(($paymentInst->getConfigData('useIFrame') && $paymentInst->getConfigData('useSeamless')) &&
                ($paymentInst->getConfigData('detect_layout') == false || ($paymentInst->getConfigData('detect_layout') == true && $detectLayout->isMobile() == false)))
            {
                $session = $this->getCheckout();
                $session->setIsQMoreIframe(true);
                $result['isIframe'] = true;
            }
            else
            {
                $result['isIframe'] = false;
            }
        }
        else
        {
            $result['isIframe'] = false;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Wirecard Checkout Page returns POST variables to this action
     */
    public function checkresponseAction()
    {

        try {
            $session = $this->getCheckout();
            $order = Mage::getModel('sales/order');
            $order->load($session->getLastOrderId());

                // load order ID
            if(!$order->getId())
                throw new Exception('Order ID not found.');

            // the customer has canceled the payment. show cancel message.
            if ($order->isCanceled())
            {
                $quoteId = $session->getLastQuoteId();
                if ($quoteId) {
                    $quote = Mage::getModel('sales/quote')->load($quoteId);
                    if ($quote->getId()) {
                        $quote->setIsActive(true)->save();
                        $session->setQuoteId($quoteId);
                    }
                }
                if(!$consumerMessage = $order->getPayment()->getAdditionalInformation('consumerMessage'))
                {
                    //fallback message if no consumerMessage has been set
                    $consumerMessage = 'Order has been canceled.';
                }
                throw new Exception(Mage::helper('wirecard_checkout_page')->__($consumerMessage));
            }

                // get sure order status has changed since redirect
            if ($order->getStatus() == Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW)
                throw new Exception(Mage::helper('wirecard_checkout_page')->__('Sorry, your payment has not confirmed by the payment provider.'));

            if ($order->getStatus() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT)
            {
                $msg = Mage::helper('wirecard_checkout_page')->__('Your order will be processed as soon as we get the payment confirmation from you bank.');
                Mage::getSingleton('checkout/session')->addNotice($msg);
            }

            // payment is okay. show success page.
            //$this->_redirect('checkout/onepage/success');
            $this->getCheckout()->setLastSuccessQuoteId($session->getLastQuoteId());
            $this->getCheckout()->setResponseRedirectUrl('checkout/onepage/success');
        }
        catch (Exception $e)
        {
            $this->getCheckout()->addNotice($e->getMessage());
            $this->getCheckout()->setResponseRedirectUrl('checkout/cart/');
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Process transaction confirm message
     */
    public function confirmAction()
    {
        try
        {
            /** verify call */
            $aResponse = $this->_checkReturnedPost();
            $data = $aResponse['post'];
            // process action
            $this->_processConfirmState($data['paymentState'], $data);
            $this->order->save();
            // send confirmation for status change
            die('<QPAY-CONFIRMATION-RESPONSE result="OK" />');
        }
        catch (Exception $e)
        {
            $orderId = (!empty($data['orderId'])) ? $data['orderId'] : '';
            Mage::log('Wirecard Checkout Page transaction status update failed: '.$e->getMessage(). '('.$orderId.')');
            Mage::log($e->getMessage() . "\n" . $e->getTraceAsString(), null, 'wirecard_checkout_page_exception.log');
            die('<QPAY-CONFIRMATION-RESPONSE result="NOK" message="' . $e->getMessage() . '" />');
        }
    }
    /**
     * check if order already has been successfully processed.
     * @param $order Mage_Sales_Model_Order
     * @return bool
     */
    protected function _succeeded($order)
    {
        $history = $order->getAllStatusHistory();
        $paymentInst =  $order->getPayment()->getMethodInstance();
        if($paymentInst)
        {
            foreach($history AS $entry)
            {
                if($entry->getStatus() == Mage_Sales_Model_Order::STATE_PROCESSING)
                {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checking POST variables.
     * Creating invoice if payment was successfull or cancel order if payment was declined
     *
     * @param bool $seamless
     * @return array
     * @throws Exception
     */
    protected function _checkReturnedPost($seamless = false)
    {
        if (!$this->getRequest()->isPost())
            throw new Exception('Not a POST message.');

        $data = $this->getRequest()->getPost();

        Mage::log('data:', print_r($data, true));

        if (empty($data) || empty($data['orderId']))
            throw new Exception('POST data is empty.');

            // load order
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($data['orderId']);
        if (!$order->getId())
            throw new Exception('Order ID not found.');
        $this->order = $order;

        $paymentInst = $order->getPayment()->getMethodInstance();
        $paymentInst->setResponse($data);
        $this->paymentInst = $paymentInst;

        if ($data['paymentState'] == 'SUCCESS' && !$seamless)
        {
            if (!$paymentInst->parseResponse())
                throw new Exception('Security key does not match.');
        }

        $this->paymentInst = $paymentInst;

        if ($seamless) {
            return array('post' => $data, 'raw' => $this->getRequest()->getRawBody());
        }
        return array('post' => $data, 'raw' => $this->getRequest()->getRawBody());
    }

    /**
     * Confirm Controller for Seamless Payments
     */
    public function seamlessConfirmAction()
    {
        try {
            $data            = $this->_checkReturnedPost(true);
            $storeId         = $this->order->getStoreId();
            $methodCode      = $this->paymentInst->getCode();
            $secretKey       = Mage::getStoreConfig('payment/'.$methodCode.'/secret_key', $storeId);
            $confirmResponse = WirecardCEE_Client_QPay_Return::generateConfirmResponseString();
            $return          = WirecardCEE_Client_QPay_Return::createReturnInstance($data['raw'], $secretKey);

            if($return->validate()) {
                $this->_confirmState($data['post'], $return);

            } else {
                throw new Exception('Unhandled Wirecard Checkout Seamless action "'.$data['paymentState'].'".');
            }

            $this->order->save();
            // send confirmation for status change
            die($confirmResponse);

        } catch (Exception $e) {
            $orderId = (!empty($data['orderId'])) ? $data['orderId'] : '';
            Mage::log('Wirecard Checkout Page transaction status update failed: '.$e->getMessage(). '('.$orderId.')');
            Mage::log($e->getMessage() . "\n" . $e->getTraceAsString(), null, 'wirecard_checkout_page_exception.log');
            $confirmResponse = WirecardCEE_Client_QPay_Return::generateConfirmResponseString($e->getMessage());
            die($confirmResponse);
        }
    }

    /**
     * Check the state response of the payment and perform corresponding order action
     *
     * @param array $data The POST data from the payment response
     * @param object $return An WirecardCEE_Client_QPay_Return_* object
     * @throws Exception
     */
    protected function _confirmState($data, $return)
    {
        switch ($return->getPaymentState()) {
            case 'SUCCESS':
            case 'PENDING':
                $this->_confirmOrder($data);
                break;

            case 'CANCEL':
                $this->_cancelOrder();
                break;

            case 'FAILURE':
                $msg = array();
                $consumerMessage = array();
                foreach ($return->getErrors() as $error) {
                    $msg[] = 'Wirecard Checkout Page Error: ' . $error->getMessage();
                    $consumerMessage[] = $error->getConsumerMessage();
                }

                if (!$this->_succeeded($this->order)) {
                    $this->order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('wirecard_checkout_page')->__('An error occured during the payment process:').' '.implode('|', $msg))->save();
                    $this->order->cancel();
                    $payment = $this->order->getPayment();
                    $payment->setAdditionalInformation('consumerMessage', implode(' ' ,$consumerMessage));
                }
                break;

            default:
                throw new Exception('Unknown paymentState: '.$data['paymentState']);
        }
    }

    /**
     * Cancel an order
     */
    protected function _cancelOrder()
    {
        if (!$this->_succeeded($this->order)) {
            if ($this->order->canUnhold()) {
                $this->order->unhold();
            }
            $this->order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('wirecard_checkout_page')->__('Customer canceled the payment process'))->save();
            $this->order->cancel();
        }
    }

    /**
     * Confirm the payment of an order
     *
     * @param array $data
     */
    protected function _confirmOrder($data)
    {
        if (!$this->_succeeded($this->order)) {
            if ($data['paymentState'] == 'PENDING') {
                $this->order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('wirecard_checkout_page')->__('The payment authorization is pending.'))->save();
            } else {
                $this->order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, Mage::helper('wirecard_checkout_page')->__('The amount has been authorized and captured by Wirecard Checkout Page.'))->save();
                // invoice payment
                if ($this->order->canInvoice()) {

                    $invoice = $this->order->prepareInvoice();
                    $invoice->register()->capture();
                    Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                }
                // send new order email to customer
                $this->order->sendNewOrderEmail();
            }
        }
        $payment                     = $this->order->getPayment();
        $additionalInformation       = Array();
        $additionalInformationString = '';

        foreach($data as $fieldName => $fieldValue) {
            switch ($fieldName) {
                case 'amount':
                case 'currency':
                case 'language':
                case 'responseFingerprint':
                case 'responseFingerprintOrder':
                case 'form_key':
                case 'paymentState':
                case 'orderId':
                    break;

                default:
                    $additionalInformation[htmlentities($fieldName)] = htmlentities($fieldValue);
                    $additionalInformationString                    .= ' | '.$fieldName.' - '.$fieldValue;
            }
        }

        if (count($additionalInformation) != 0) {

            $payment->setAdditionalInformation($additionalInformation);
            $payment->setAdditionalData(serialize($additionalInformation));

            if ($payment->hasAdditionalInformation()) {
                Mage::log('Added Additional Information to Order ' . $data['orderId'] . ' :'. $additionalInformationString);
            }
        }
        $payment->save();
    }

    /**
     * Store anonymized Payment Data from Seamless Checkout in the Session
     */
    public function saveSessInfoAction()
    {
        $payment = $this->getRequest()->getPost();

        if (!empty($payment) && isset($payment['payment']) && !empty($payment['payment'])) {
            $payment = $payment['payment'];
            if (!Mage::getStoreConfigFlag('payment/wirecard_checkout_page_cc/pci3DssSaqAEnable', Mage::app()->getStore()->getId()))
            {
                if (($payment['method']=='wirecard_checkout_page_cc' || $payment['method']=='wirecard_checkout_page_ccMoto') && !empty($payment['cc_owner']) && !empty($payment['cc_type'])
                    && !empty($payment['cc_number']) && !empty($payment['cc_exp_month']) && !empty($payment['cc_exp_year'])
                    && !empty($payment['additional_data'])) {
                    Mage::getSingleton('core/session')->setWirecardCheckoutPagePaymentInfo($payment);
                }
            }
        }

        return;
    }

    /**
     * Read paymentinformation from datastorage
     */
    public function readDatastorageAction()
    {
        /** @var Phoenix_WirecardCheckoutPage_Helper_Data $helper */
        $helper = Mage::helper('wirecard_checkout_page');
        $payment = $this->getRequest()->getPost();

        $ret = new \stdClass();
        $ret->status = WirecardCEE_Client_DataStorage_Response_Read::STATE_NOT_EXISTING;
        $ret->paymentInformaton = Array();

        if (!empty($payment) && isset($payment['payment']) && !empty($payment['payment'])) {
            $payment = $payment['payment'];

            if ($payment['method']=='wirecard_checkout_page_cc' || $payment['method']=='wirecard_checkout_page_ccMoto') {
                $methodCode = $payment['method'];
                $storeId    = Mage::app()->getStore()->getId();
                $shopId     = Mage::getStoreConfig('payment/'.$methodCode.'/shop_id', $storeId);
                $customerId = Mage::getStoreConfig('payment/'.$methodCode.'/customer_id', $storeId);

                $readResponse = $helper->readWirecardCEE($customerId, $storeId, $shopId, $payment['method']);
                if($readResponse)
                {
                    $ret->status = $readResponse->getStatus();
                    $ret->paymentInformaton = $readResponse->getPaymentInformation();
                }

            }
        }

        print json_encode($ret);

    }


    /**
     * Delete the anonymized Wirecard Checkout Page Session Data stored from Seamless Checkout
     */
    public function deleteSessInfoAction()
    {
        if ($this->getRequest()->isPost()) {
            Mage::getSingleton('core/session')->unsetData('wirecard_checkout_page_payment_info');
        }
        return;
    }

    /**
     * The controller action used for older browsers to return datastorage parameters in an iFrame.
     */
    public function storereturnAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _processConfirmState($state, $data)
    {
        switch ($state) {
            case 'SUCCESS':
            case 'PENDING':
                $this->_confirmOrder($data);
                break;

            case 'CANCEL':
                $this->_cancelOrder();
                break;

            case 'FAILURE':
                $msg = (!empty($data['message'])) ? $data['message'] : '';
                if (!$this->_succeeded($this->order)) {
                    $this->order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('wirecard_checkout_page')->__('An error occured during the payment process:').' '.$msg)->save();
                    $this->order->cancel();
                    $payment = $this->order->getPayment();
                    $payment->setAdditionalInformation('consumerMessage', $data['consumerMessage']);
                }
                break;

            default:
                throw new Exception('Unhandled Wirecard Checkout Page action "'.$data['paymentState'].'".');
        }
    }
}
