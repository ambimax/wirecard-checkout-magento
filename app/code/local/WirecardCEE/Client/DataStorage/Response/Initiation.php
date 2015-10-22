<?php

class WirecardCEE_Client_DataStorage_Response_Initiation
    extends WirecardCEE_Client_Response_Abstract
{
    const STATE_SUCCESS = 0;
    const STATE_FAILURE = 1;

    protected static $STORAGE_ID = 'storageId';
    protected static $JAVASCRIPT_URL = 'javascriptUrl';

    /**
     * getter for the Response status
     * values: 0 ... success
     *         1 ... failure
     * @return int
     */
    public function getStatus()
    {
        if($this->_getField(self::$STORAGE_ID))
        {
            return self::STATE_SUCCESS;
        }
        else
        {
            return self::STATE_FAILURE;
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