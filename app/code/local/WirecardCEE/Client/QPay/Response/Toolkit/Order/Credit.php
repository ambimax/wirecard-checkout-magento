<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Credit-object returned by an {@link WirecardCEE_Client_QPay_Request_Toolkit::getOrderDetails()} operation.
 */
final class WirecardCEE_Client_QPay_Response_Toolkit_Order_Credit
    extends WirecardCEE_Client_QPay_Response_Toolkit_FinancialObject
{
    private static $MERCHANT_NUMBER = 'merchantNumber';
    private static $CREDIT_NUMBER = 'creditNumber';
    private static $ORDER_NUMBER = 'orderNumber';
    private static $BATCH_NUMBER = 'batchNumber';
    private static $AMOUNT = 'amount';
    private static $CURRENCY = 'currency';
    private static $TIME_CREATED = 'timeCreated';
    private static $TIME_MODIFIED = 'timeModified';
    private static $STATE = 'state';
    private static $OPERATIONS_ALLOWED = 'operationsAllowed';
    private static $GATEWAY_REFERENCE_NUMBER = 'gatewayReferenceNumber';

    /**
     * creates an instance of an {@link WirecardCEE_Client_QPay_Response_Toolkit_Order_Credit} object
     * @param string[] $creditData
     */
    public function __construct($creditData)
    {
        $this->_data = $creditData;
    }

    /**
     * getter for credits merchant number
     * @return string
     */
    public function getMerchantNumber()
    {
        return $this->_getField(self::$MERCHANT_NUMBER);
    }

    /**
     * getter for credit number
     * @return string
     */
    public function getCreditNumber()
    {
        return $this->_getField(self::$CREDIT_NUMBER);
    }

    /**
     * getter for the corresponding order number
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->_getField(self::$ORDER_NUMBER);
    }

    /**
     * getter for the corresponding batch number
     * @return string
     */
    public function getBatchNumber()
    {
        return $this->_getField(self::$BATCH_NUMBER);
    }

    /**
     * getter for the credit amount
     * @return string
     */
    public function getAmount()
    {
        return $this->_getField(self::$AMOUNT);
    }

    /**
     * getter for the credit currency
     * @return string
     */
    public function getCurrency()
    {
        return $this->_getField(self::$CURRENCY);
    }

    /**
     * getter for the creation time
     * @return DateTime
     */
    public function getTimeCreated()
    {
        return DateTime::createFromFormat(self::$DATETIME_FORMAT, $this->_getField(self::$TIME_CREATED));
    }

    /**
     * getter for the last time this credit has been updated
     * @return DateTime
     */
    public function getTimeModified()
    {
        return DateTime::createFromFormat(self::$DATETIME_FORMAT, $this->_getField(self::$TIME_MODIFIED));
    }

    /**
     * getter for the currenc credit state
     * @return string
     */
    public function getState()
    {
        return $this->_getField(self::$STATE);
    }

    /**
     * getter for the allowed follow-up operations
     * @return string[]
     */
    public function getOperationsAllowed()
    {
        return explode(',', $this->_getField(self::$OPERATIONS_ALLOWED));
    }

    /**
     * getter for the gateway reference number
     * @return string
     */
    public function getGatewayReferenceNumber()
    {
        return $this->_getField(self::$GATEWAY_REFERENCE_NUMBER);
    }
}
