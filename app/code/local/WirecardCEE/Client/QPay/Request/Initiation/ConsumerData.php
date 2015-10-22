<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * container class for consumerData used in {@link WirecardCEE_Client_QPay_Request_Initiation}
 */
class WirecardCEE_Client_QPay_Request_Initiation_ConsumerData
{
    protected $_addressData = Array();
    
    protected static $PREFIX = 'consumer';
    protected static $EMAIL = 'Email';
    protected static $BIRTH_DATE = 'BirthDate';
    protected static $TAX_IDENTIFICATION_NUMBER = 'TaxIdentificationNumber';
    protected static $DRIVERS_LICENSE_NUMBER = 'DriversLicenseNumber';
    protected static $DRIVERS_LICENSE_COUNTRY = 'DriversLicenseCountry';
    protected static $DRIVERS_LICENSE_STATE = 'DriversLicenseState';
    protected static $IP_ADDRESS = 'IpAddress';
    protected static $USER_AGENT = 'UserAgent';
    
    protected static $BIRTH_DATE_FORMAT = 'Y-m-d';
    
    /**
     * setter for the mail address of the consumer
     * @param string $mailAddress
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setEmail($mailAddress)
    {
        $this->_setField(self::$EMAIL, $mailAddress);
        return $this;
    }
    
    /**
     * setter for the birthdate of the consumer
     * @param DateTime $birthDate
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setBirthDate(DateTime $birthDate)
    {
        $this->_setField(self::$BIRTH_DATE, $birthDate->format(self::$BIRTH_DATE_FORMAT));
        return $this;
    }
    
    /**
     * setter for the tax identification number of the consumer
     * @param string $taxIdentificationNumber
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setTaxIdentificationNumber($taxIdentificationNumber)
    {
        $this->_setField(self::$TAX_IDENTIFICATION_NUMBER, $taxIdentificationNumber);
        return $this;
    }
    
    /**
     * setter for the drivers license number of the consumer
     * @param string $driversLicenseNumber
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setDriversLicenseNumber($driversLicenseNumber)
    {
        $this->_setField(self::$DRIVERS_LICENSE_NUMBER, $driversLicenseNumber);
        return $this;
    }
    
    /**
     * setter for the drivers license country of the consumer
     * @param string $driversLicenseCountry
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setDriversLicenseCountry($driversLicenseCountry)
    {
        $this->_setField(self::$DRIVERS_LICENSE_COUNTRY, $driversLicenseCountry);
        return $this;
    }
    
    /**
     * setter for the drivers license state of the consumer
     * @param string $driversLicenseState
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setDriversLicenseState($driversLicenseState)
    {
        $this->_setField(self::$DRIVERS_LICENSE_STATE, $driversLicenseState);
        return $this;
    }
    
    /**
     * adds addressinformation to the consumerdata.
     * used {@link WirecardCEE_Client_QPay_Initiation_ConsumerData_Address::getData()}
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address $address
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function addAddressInformation(WirecardCEE_Client_QPay_Request_Initiation_ConsumerData_Address $address)
    {
        $consumerData = array_merge($this->_addressData, $address->getData());
        $this->_addressData = $consumerData;
        return $this;
    }

    /**
     * setter for the consumer IP-Address
     * @param string $consumerIpAddress
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setIpAddress($consumerIpAddress)
    {
        $this->_setField(self::$IP_ADDRESS, $consumerIpAddress);
        return $this;
    }

    /**
     * setter for the consumer user-agent
     * @param string $consumerUserAgent
     * @return WirecardCEE_Client_QPay_Request_Initiation_ConsumerData 
     */
    public function setUserAgent($consumerUserAgent)
    {
        $this->_setField(self::$USER_AGENT, $consumerUserAgent);
        return $this;
    }

    /**
     * setter for consumerdata fields
     * @param string $name
     * @param string $value 
     * @access private
     */
    protected function _setField($name, $value)
    {
        //e.g. consumerBillingFirstname
        $this->_addressData[self::$PREFIX . $name] = strval($value);
    }

    /**
     * getter for all consumerData
     * @return string[]
     */
    public function getData()
    {
        return $this->_addressData;
    }
}