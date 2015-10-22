<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * container class for Addresses used for initiation.
 * @see WirecardCEE_Client_QPay_Initiation_ConsumerData
 * Used for shipping and billing consumerData
 */
class WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
{
    const TYPE_SHIPPING = 'Shipping';
    const TYPE_BILLING = 'Billing';
    
    protected static $PREFIX = 'consumer';
    protected static $FIRSTNAME = 'Firstname';
    protected static $LASTNAME = 'Lastname';
    protected static $ADDRESS1 = 'Address1';
    protected static $ADDRESS2 = 'Address2';
    protected static $CITY = 'City';
    protected static $COUNTRY = 'Country';
    protected static $STATE = 'State';
    protected static $ZIP_CODE = 'ZipCode';
    protected static $PHONE = 'Phone';
    protected static $FAX = 'Fax';
    
    protected $_addressType;
    protected $_addressData = Array();
    
    /**
     * creates an instance of the WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address object.
     * addressType should be Shipping or Billing.
     * @param string $addressType
     */
    public function __construct($addressType) 
    {
        $this->_addressType = $addressType;
    }
    
    /**
     * setter for the firstname used for the given address.
     * @param string $firstname
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setFirstname($firstname)
    {
        $this->_setField(self::$FIRSTNAME, $firstname);
        return $this;
    }

    /**
     * setter for the lastname used for the given address.
     * @param string $lastname
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setLastname($lastname)
    {
        $this->_setField(self::$LASTNAME, $lastname);
        return $this;
    }
    
    /**
     * setter for the addressfield 1 used for the given address.
     * @param string $address1
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setAddress1($address1)
    {
        $this->_setField(self::$ADDRESS1, $address1);
        return $this;
    }
    
    /**
     * setter for the addressfield 2 used for the given address.
     * @param string $address2
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setAddress2($address2)
    {
        $this->_setField(self::$ADDRESS2, $address2);
        return $this;
    }
    
    /**
     * setter for the city used for the given address.
     * @param string $city
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setCity($city)
    {
        $this->_setField(self::$CITY, $city);
        return $this;
    }
    
    /**
     * setter for the country used for the given address.
     * @param string $country
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setCountry($country)
    {
        $this->_setField(self::$COUNTRY, $country);
        return $this;
    }
    
    /**
     * setter for the state used for the given address.
     * @param string $state
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setState($state)
    {
        $this->_setField(self::$STATE, $state);
        return $this;
    }
    
    /**
     * setter for the zip code used for the given address.
     * @param string $zipCode
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setZipCode($zipCode)
    {
        $this->_setField(self::$ZIP_CODE, $zipCode);
        return $this;
    }
    
    /**
     * setter for the phone number used for the given address.
     * @param string $phone
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setPhone($phone)
    {
        $this->_setField(self::$PHONE, $phone);
        return $this;
    }
    
    /**
     * setter for the fax number used for the given address.
     * @param string $fax
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address
     */
    public function setFax($fax)
    {
        $this->_setField(self::$FAX, $fax);
        return $this;
    }
    
    /**
     * setter for an addressfield.
     * @param string $name
     * @param string $value 
     * @access private
     */
    protected function _setField($name, $value)
    {
        //e.g. consumerBillingFirstname
        $this->_addressData[self::$PREFIX . $this->_addressType . $name] = strval($value);
    }
    
    /**
     * returns the given addressfields as an array
     * @return string[] 
     */
    public function getData()
    {
        return $this->_addressData;
    }
}