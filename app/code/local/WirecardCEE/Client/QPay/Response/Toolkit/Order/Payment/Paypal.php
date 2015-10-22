<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * PayPal Payment-object returned by an {@link WirecardCEE_Client_QPay_Request_Toolkit::getOrderDetails()} operation.
 */
final class WirecardCEE_Client_QPay_Response_Toolkit_Order_Payment_Paypal
    extends WirecardCEE_Client_QPay_Response_Toolkit_Order_Payment
{

    private static $PAYER_ID = 'paypalPayerID';
    private static $PAYER_EMAIL = 'paypalPayerEmail';
    private static $PAYER_FIRST_NAME = 'paypalPayerFirstName';
    private static $PAYER_LAST_NAME = 'paypalPayerLastName';
    private static $PAYER_ADDRESS_COUNTRY = 'paypalPayerAddressCountry';
    private static $PAYER_ADDRESS_CITY = 'paypalPayerAddressCity';
    private static $PAYER_ADDRESS_STATE = 'paypalPayerAddressState';
    private static $PAYER_ADDRESS_NAME = 'paypalPayerAddressName';
    private static $PAYER_ADDRESS_STREET_1 = 'paypalPayerAddressStreet1';
    private static $PAYER_ADDRESS_STREET_2 = 'paypalPayerAddressStreet2';
    private static $PAYER_ADDRESS_ZIP = 'paypalPayerAddressZIP';
    private static $PAYER_ADDRESS_STATUS = 'paypalPayerAddressStatus';
    private static $PROTECTION_ELIGIBILITY = 'paypalProtectionEligibility';

    /**
     * getter for PayPal payerID
     * @return string
     */
    public function getPayerId()
    {
        return $this->_getField(self::$PAYER_ID);
    }

    /**
     * getter for PayPal payer email
     * @return string
     */
    public function getPayerEmail()
    {
        return $this->_getField(self::$PAYER_EMAIL);
    }

    /**
     * getter for PayPal payer firstname
     * @return string
     */
    public function getPayerFirstName()
    {
        return $this->_getField(self::$PAYER_FIRST_NAME);
    }

    /**
     * getter for PayPal payer lastname
     * @return string
     */
    public function getPayerLastName()
    {
        return $this->_getField(self::$PAYER_LAST_NAME);
    }

    /**
     * getter for PayPal payer country address field
     * @return string
     */
    public function getPayerAddressCountry()
    {
        return $this->_getField(self::$PAYER_ADDRESS_COUNTRY);
    }

    /**
     * getter for PayPal payer city address field
     * @return string
     */
    public function getPayerAddressCity()
    {
        return $this->_getField(self::$PAYER_ADDRESS_CITY);
    }

    /**
     * getter for PayPal payer state address field
     * @return string
     */
    public function getPayerAddressState()
    {
        return $this->_getField(self::$PAYER_ADDRESS_STATE);
    }

    /**
     * getter for PayPal payer name address field
     * @return string
     */
    public function getPayerAddressName()
    {
        return $this->_getField(self::$PAYER_ADDRESS_NAME);
    }

    /**
     * getter for PayPal payer street 1 address field
     * @return string
     */
    public function getPayerAddressStreet1()
    {
        return $this->_getField(self::$PAYER_ADDRESS_STREET_1);
    }

    /**
     * getter for PayPal payer street 2 address field
     * @return string
     */
    public function getPayerAddressStreet2()
    {
        return $this->_getField(self::$PAYER_ADDRESS_STREET_2);
    }

    /**
     * getter for PayPal payer zipcode address field
     * @return string
     */
    public function getPayerAddressZip()
    {
        return $this->_getField(self::$PAYER_ADDRESS_ZIP);
    }

    /**
     * getter for PayPal payer address status
     * @return string
     */
    public function getPayerAddressStatus()
    {
        return $this->_getField(self::$PAYER_ADDRESS_STATUS);
    }

    /**
     * getter for PayPal protection eligibility
     * @return string
     */
    public function getProtectionEligibility()
    {
        return $this->_getField(self::$PROTECTION_ELIGIBILITY);
    }
}