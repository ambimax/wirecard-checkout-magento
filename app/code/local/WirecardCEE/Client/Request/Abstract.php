<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/
if(!class_exists('Zend_Http_Client', false))
{
    require_once 'Zend/Http/Client.php';
}

abstract class WirecardCEE_Client_Request_Abstract
{
    protected $_secret;
    protected $_fingerprintOrderString;
    protected $_httpClient;
    protected $_fingerprintOrderType = '';
    protected $_fingerprintString = null;
    protected $_fingerprintOrder = Array();
    protected $_requestData;

    protected static $CUSTOMER_ID = 'customerId';
    protected static $SECRET = 'secret';
    protected static $LANGUAGE = 'language';
    protected static $SHOP_ID = 'shopId';
    protected static $REQUEST_FINGERPRINT_ORDER = 'requestFingerprintOrder';
    protected static $REQUEST_FINGERPRINT = 'requestFingerprint';
    protected static $AMOUNT = 'amount';
    protected static $CURRENCY = 'currency';
    protected static $ORDER_DESCRIPTION = 'orderDescription';
    protected static $AUTO_DEPOSIT = 'autoDeposit';
    protected static $ORDER_NUMBER = 'orderNumber';
    protected static $FINGERPRINT_TYPE_DYNAMIC = 0;
    protected static $FINGERPRINT_TYPE_FIXED = 1;

    protected static $BOOL_TRUE = 'yes';
    protected static $BOOL_FALSE = 'no';

    // Client library Version
    protected static $LIBRARY_NAME = 'WirecardCEEClientLibrary';
    protected static $LIBRARY_VERSION = '2.0.1QMORE';

    protected static $FRAMEWORK_NAME = 'Zend Framework';

    protected $_requestPath = '';

    /**
     * abstract constructor for WirecardCEE_Client_Request_Initiation objects.
     * @param string $customerId
     * @param string $shopId
     * @param string $language
     */
    public function __construct($customerId, $shopId, $language)
    {
        $this->_setField(self::$CUSTOMER_ID, $customerId);
        $this->_setField(self::$SHOP_ID, $shopId);
        $this->_setField(self::$LANGUAGE, $language);
    }

    protected function _setSecret($secret)
    {
        $this->_secret = $secret;
        $this->_fingerprintOrder[] = self::$SECRET;
    }

    /**
     * sends the qpay request and returns the zend http response object instance
     * @throws WirecardCEE_Client_Exception
     * @access private
     * @return Zend_Http_Response
     */
    protected function _send()
    {
        if(!empty($this->_fingerprintOrder))
        {
            $this->_fingerprintString = $this->_calculateFingerprint();
            $this->_addFingerprintFieldsToRequest();
        }

        try
        {
            $response = $this->_sendRequest();
        }
        catch (Zend_Http_Client_Exception $e)
        {
            throw new WirecardCEE_Client_Exception($e->getMessage(), $e->getCode(), $e);
        }
        return $response;
    }

    /**
     * method to calculate md5 fingerprintstring from given fields.
     * @return md5 fingerprint hash
     * @access private
     */
    protected function _calculateFingerprint()
    {
        $fingerprintOrder = $this->_fingerprintOrder;
        if($this->_fingerprintOrderType == self::$FINGERPRINT_TYPE_DYNAMIC)
        {
            //we have to add REQUESTFINGERPRINTORDER to local fingerprintOrder to add correct value to param list
            $fingerprintOrder[] = self::$REQUEST_FINGERPRINT_ORDER;
            $requestFingerprintOrder = WirecardCEE_Fingerprint::fingerprintOrderToString($fingerprintOrder);
            $this->_setField(self::$REQUEST_FINGERPRINT_ORDER, $requestFingerprintOrder);
        }
        //fingerprintFields == requestFields + secret - secret MUST NOT be send as param
        $fingerprintFields = $this->_getFingerprintFields();
        return WirecardCEE_Fingerprint::generate($fingerprintFields, $fingerprintOrder);
    }

    /**
     * adds the fingerprintfields requestFingerprint and requestFingerprintOrder to the request array
     */
    protected function _addFingerprintFieldsToRequest()
    {
        if($this->_fingerprintString != null)
        {
            $this->_requestData[self::$REQUEST_FINGERPRINT] = $this->_fingerprintString;
        }
    }

    /**
     * sends the qpay request and returns the zend http response object instance
     * @throws WirecardCEE_Client_QPay_Exception
     * @access private
     * @return Zend_Http_Response
     */
    protected function _sendRequest()
    {
        $httpClient = $this->_getZendHttpClient();
        $httpClient->setParameterPost($this->_requestData);
        return $httpClient->request(Zend_Http_Client::POST);
    }

    protected function _getFingerprintFields()
    {
        $data = $this->_requestData;
        $data[self::$SECRET] = $this->_secret;
        return $data;
    }

    /**
     * setter for requestfield.
     * @param $name
     * @param $value
     */
    protected function _setField($name, $value)
    {
        $this->_requestData[strval($name)] = strval($value);
        $this->_fingerprintOrder[] = strval($name);
    }

    /**
     * setter for Zend_Http_Client.
     * Use this if you need specific client-configuration.
     * otherwise the clientlibrary instantiates the Zend_Http_Client on its own.
     * @param Zend_Http_Client $httpClient
     * @return WirecardCEE_Client_QPay_Request_Abstract
     */
    public function setZendHttpClient(Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    abstract protected function _getRequestUrl();

        /**
     * private getter for the Zend_Http_Client
     * if not set yet it will be instantiated
     * @access private
     * @return Zend_Http_Client
     */
    protected function _getZendHttpClient()
    {
        if($this->_httpClient == null)
        {
            $this->setZendHttpClient(new Zend_Http_Client($this->_getRequestUrl()));
        }
        else
        {
            $this->_httpClient->resetParameters(true);
            $this->_httpClient->setUri($this->_getRequestUrl());
        }
        return $this->_httpClient;
    }
}
