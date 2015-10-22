<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

final class WirecardCEE_Client_QPay_Request_Toolkit
    extends WirecardCEE_Client_QPay_Request_Abstract
{

    //qpay params
    protected static $PASSWORD = 'password';
    protected static $PAYMENT_NUMBER = 'paymentNumber';
    protected static $CREDIT_NUMBER = 'creditNumber';
    protected static $SOURCE_ORDER_NUMBER = 'sourceOrderNumber';
    protected static $COMMAND = 'command';

    //command values
    protected static $COMMAND_APPROVE_REVERSAL = 'approveReversal';
    protected static $COMMAND_DEPOSIT = 'deposit';
    protected static $COMMAND_DEPOSIT_REVERSAL = 'depositReversal';
    protected static $COMMAND_GET_ORDER_DETAILS = 'getOrderDetails';
    protected static $COMMAND_RECUR_PAYMENT = 'recurPayment';
    protected static $COMMAND_REFUND = 'refund';
    protected static $COMMAND_REFUND_REVERSAL = 'refundReversal';

    protected $_fingerprintOrderType = 1;
    protected $_command = '';

    /**
     * creates an instance of an WirecardCEE_Client_QPay_Request_Toolkit object.
     * used for toolkit operations.
     * @param string $customerId
     * @param string $secret
     * @param string $password
     * @param string $language
     * @param string $shopId
     * @param string[] $config
     */
    public function __construct($customerId, $shopId, $secret, $password, $language, $pluginVersion)
    {
        $this->_setField(self::$PASSWORD, $password);
        parent::__construct($customerId, $shopId, $secret, $language, $pluginVersion);
    }

    /**
     * executes an approveReversal operation
     * @param string $orderNumber
     * @return WirecardCEE_Client_QPay_Response_Toolkit_ApproveReversal
     */
    public function approveReversal($orderNumber)
    {
        $this->_setField(self::$ORDER_NUMBER, $orderNumber);
        $this->_command = self::$COMMAND_APPROVE_REVERSAL;
        $this->_fingerprintOrder = Array('customerId', 'shopId', 'password',
                                         'secret', 'language',
                                         'orderNumber');
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Toolkit_ApproveReversal($result);
    }

    /**
     * executes an deposit operation
     * @param string $orderNumber
     * @param string $amount
     * @param string $currency
     * @return WirecardCEE_Client_QPay_Response_Toolkit_Deposit
     */
    public function deposit($orderNumber, $amount, $currency)
    {
        $this->_setField(self::$ORDER_NUMBER, $orderNumber);
        $this->_setField(self::$AMOUNT, $amount);
        $this->_setField(self::$CURRENCY, $currency);
        $this->_command = self::$COMMAND_DEPOSIT;
        $this->_fingerprintOrder = Array('customerId', 'shopId', 'password',
                                         'secret', 'language',
                                         'orderNumber', 'amount', 'currency');
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Toolkit_Deposit($result);
    }

    /**
     * executes an depositReversal operation
     * @param string $orderNumber
     * @param string $paymentNumber
     * @return WirecardCEE_Client_QPay_Response_Toolkit_DepositReversal
     */
    public function depositReversal($orderNumber, $paymentNumber)
    {
        $this->_setField(self::$ORDER_NUMBER, $orderNumber);
        $this->_setField(self::$PAYMENT_NUMBER, $paymentNumber);
        $this->_command = self::$COMMAND_DEPOSIT_REVERSAL;
        $this->_fingerprintOrder = Array('customerId', 'shopId', 'password',
                                         'secret', 'language',
                                         'orderNumber', 'paymentNumber');
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Toolkit_DepositReversal($result);
    }

    /**
     * executes an getOrderDetails operation
     * @param string $orderNumber
     * @return WirecardCEE_Client_QPay_Response_Toolkit_GetOrderDetails
     */
    public function getOrderDetails($orderNumber)
    {
        $this->_setField(self::$ORDER_NUMBER, $orderNumber);
        $this->_command = self::$COMMAND_GET_ORDER_DETAILS;
        $this->_fingerprintOrder = Array('customerId', 'shopId', 'password',
                                         'secret', 'language',
                                         'orderNumber');
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Toolkit_GetOrderDetails($result);
    }

    /**
     * executes an recurPayment operation
     * @param string $sourceOrderNumber
     * @param string $amount
     * @param string $currency
     * @param bool $depositFlat
     * @param string $orderDescription
     * @return WirecardCEE_Client_QPay_Response_Toolkit_RecurPayment
     */
    public function recurPayment($sourceOrderNumber, $amount, $currency, $depositFlag, $orderDescription)
    {
        $this->_setField(self::$SOURCE_ORDER_NUMBER, $sourceOrderNumber);
        $this->_setField(self::$AMOUNT, $amount);
        $this->_setField(self::$CURRENCY, $currency);
        if($depositFlag == true)
        {
            $this->_setField(self::$AUTO_DEPOSIT, self::$BOOL_TRUE);
        }
        else
        {
            $this->_setField(self::$AUTO_DEPOSIT, self::$BOOL_FALSE);
        }
        $this->_setField(self::$ORDER_DESCRIPTION, $orderDescription);
        $this->_command = self::$COMMAND_RECUR_PAYMENT;
        $this->_fingerprintOrder = Array('customerId', 'shopId', 'password',
                                         'secret', 'language',
                                         'sourceOrderNumber', 'autoDeposit', 'orderDescription',
                                         'amount', 'currency');
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Toolkit_RecurPayment($result);
    }

    /**
     * executes an refund operation
     * @param string $orderNumber
     * @param string $amount
     * @param string $currency
     * @return WirecardCEE_Client_QPay_Response_Toolkit_Refund
     */
    public function refund($orderNumber, $amount, $currency)
    {
        $this->_setField(self::$ORDER_NUMBER, $orderNumber);
        $this->_setField(self::$AMOUNT, $amount);
        $this->_setField(self::$CURRENCY, $currency);
        $this->_command = self::$COMMAND_REFUND;
        $this->_fingerprintOrder = Array('customerId', 'shopId', 'password',
                                         'secret', 'language',
                                         'orderNumber', 'amount', 'currency');
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Toolkit_Refund($result);
    }

    /**
     * executes an refundReversal operation
     * @param string $orderNumber
     * @param string $creditNumber
     * @return WirecardCEE_Client_QPay_Response_Toolkit_RefundReversal
     */
    public function refundReversal($orderNumber, $creditNumber)
    {
        $this->_setField(self::$ORDER_NUMBER, $orderNumber);
        $this->_setField(self::$CREDIT_NUMBER, $creditNumber);
        $this->_command = self::$COMMAND_REFUND_REVERSAL;
        $this->_fingerprintOrder = Array('customerId', 'shopId', 'password',
                                         'secret', 'language',
                                         'orderNumber', 'creditNumber');
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Toolkit_RefundReversal($result);
    }

    protected function _getRequestUrl()
    {
         return WirecardCEE_Client_Configuration::loadConfiguration()->getBackendUrl().'/'.$this->_command;
    }
}