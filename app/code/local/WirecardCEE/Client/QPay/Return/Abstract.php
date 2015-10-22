<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Abstract baseclass for all return containers
 */
abstract class WirecardCEE_Client_QPay_Return_Abstract
{
    protected $_returnData;
    protected $_state = '';
    
    /**
     * @param string $returnData 
     */
    public function __construct($returnData)
    {
        $this->_returnData = $returnData;
    }
    
    /**
     * validation of returned values
     * @throws WirecardCEE_Client_QPay_Exception
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * getter for paymentState
     * @return string
     */
    public function getPaymentState()
    {
        return $this->_state;
    }
    
    /**
     * magic getter method
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $name = strval($name);
        if(array_key_exists($name, $this->_returnData))
        {
            return $this->_returnData[$name];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * getter for filtered return data.
     * @return string[]
     */
    public function getReturned()
    {
        $return = $this->_returnData;
        //noone needs the responseFingerprintOrder and responseFingerprint in the shop.
        if(array_key_exists('responseFingerprintOrder', $return) && array_key_exists('responseFingerprint', $return))
        {
            unset($return['responseFingerprintOrder']);
            unset($return['responseFingerprint']);
        }
        return $return;
    }
}