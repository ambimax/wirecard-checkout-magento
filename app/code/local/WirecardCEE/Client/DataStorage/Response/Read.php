<?php

class WirecardCEE_Client_DataStorage_Response_Read
    extends WirecardCEE_Client_Response_Abstract
{
    const STATE_EXISTING = 0;
    const STATE_NOT_EMPTY = 1;
    const STATE_NOT_EXISTING = 2;
    const STATE_FAILURE = 3;

    const PAYMENTTYPE_CLICK2PAY = 'C2P';
    const PAYMENTTYPE_CREDITCARD = 'CCARD';
    const PAYMENTTYPE_ELV = 'ELV';
    const PAYMENTTYPE_GIROPAY = 'GIROPAY';
    const PAYMENTTYPE_PAYBOX = 'PBX';

    protected static $STORAGE_ID = 'storageId';
    protected static $ERROR = 'error';
    protected static $PAYMENT_INFORMATION = 'paymentInformation';
    protected static $PAYMENT_INFORMATIONS = 'paymentInformations';
    protected static $JAVASCRIPT_URL = 'javascriptUrl';

    protected $_errors = Array();

    /**
     * getter for the Response status
     * values: 0 ... storageId exists and is empty
     *         1 ... storageId exists and not is empty
     *         2 ... storageId does not exist
     *         3 ... an error occured
     * @return int
     */
    public function getStatus()
    {
        if($this->_getField(self::$STORAGE_ID))
        {
            if($this->_getField(self::$PAYMENT_INFORMATION))
            {
                return self::STATE_NOT_EMPTY;
            }
            else
            {
                return self::STATE_EXISTING;
            }
        }
        else if($this->_getField(self::$ERRORS))
        {
            return self::STATE_FAILURE;
        }
        else
        {
            return self::STATE_NOT_EXISTING;
        }
    }

    /**
     * getter for all stored anonymized paymentInformation
     * @param string $paymentType - filter only one paymenttype
     * @return mixed[]
     */
    public function getPaymentInformation($paymentType = null)
    {
        $paymentInformation = $this->_getField(self::$PAYMENT_INFORMATION);
        if(is_array($paymentInformation))
        {
            if($paymentType != null)
            {
                $paymentType = strtoupper($paymentType);
                foreach($paymentInformation AS $singlePaymentInformation)
                {
                    if($singlePaymentInformation['paymentType'] == $paymentType)
                    {
                        return $singlePaymentInformation;
                    }
                }
                return Array();
            }
            else
            {
                return $paymentInformation;
            }
        }
        else
        {
            return Array();
        }
    }

    public function getNumberOfPaymentInformation()
    {
        return $this->_getField(self::$PAYMENT_INFORMATIONS);
    }

    public function hasPaymentInformation($paymentType)
    {
        $paymentInformation = $this->getPaymentInformation($paymentType);
        if(!empty($paymentInformation))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * getter for storageId returned by the dataStorage
     * @return string
     */
    public function getStorageId()
    {
        return $this->_getField(self::$STORAGE_ID);
    }

    /**
     * getter for javascriptUrl returned by the dataStorage
     *
     * the script behind this url is used by the shopsystem to save paymentInformation in the dataStorage
     * @return string
     */
    public function getJavascriptUrl()
    {
        return $this->_getField(self::$JAVASCRIPT_URL);
    }
}