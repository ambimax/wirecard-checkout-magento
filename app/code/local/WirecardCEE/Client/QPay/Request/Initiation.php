<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * QPAY Initiation class used for server-to-server QPAY 3.x initiations
 */
final class WirecardCEE_Client_QPay_Request_Initiation
    extends WirecardCEE_Client_QPay_Request_Abstract
{

    //qpay Params
    protected static $PAYMENT_TYPE = 'paymentType';
    protected static $SUCCESS_URL = 'successUrl';
    protected static $CANCEL_URL = 'cancelUrl';
    protected static $FAILURE_URL = 'failureUrl';
    protected static $SERVICE_URL = 'serviceUrl';
    protected static $PENDING_URL = 'pendingUrl';
    protected static $FINANCIAL_INSTITUTION = 'financialInstitution';
    protected static $DISPLAY_TEXT = 'displayText';
    protected static $CONFIRM_URL = 'confirmUrl';
    protected static $IMAGE_URL = 'imageUrl';
    protected static $WINDOW_NAME = 'windowName';
    protected static $DUPLICATE_REQUEST_CHECK = 'duplicateRequestCheck';
    protected static $CUSTOMER_STATEMENT = 'customerStatement';
    protected static $ORDER_REFERENCE = 'orderReference';
    protected static $MAX_RETRIES = 'maxRetries';
    protected static $CONFIRM_MAIL = 'confirmMail';
    protected static $CONSUMER_USER_AGENT = 'consumerUserAgent';
    protected static $CONSUMER_IP_ADDRESS = 'consumerIpAddress';
    protected static $ORDER_IDENT = 'orderIdent';
    protected static $STORAGE_ID = 'storageId';

    protected $_fingerprintOrderType = 0;

    /**
     * setter for the qpay parameter financialInstitution
     * @param string $financialInstitution
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setFinancialInstitution($financialInstitution)
    {
        $this->_setField(self::$FINANCIAL_INSTITUTION, $financialInstitution);
        return $this;
    }

    /**
     * setter for the qpay parameter displaytext
     * @param string $displayText
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setDisplayText($displayText)
    {
        $this->_setField(self::$DISPLAY_TEXT, $displayText);
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter confirmUrl
     * @param string $confirmUrl
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setConfirmUrl($confirmUrl)
    {
        $this->_setField(self::$CONFIRM_URL, $confirmUrl);
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter imageUrl
     * @param string $imageUrl
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setImageUrl($imageUrl)
    {
        $this->_setField(self::$IMAGE_URL, $imageUrl);
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter windowName
     * @param string $windowName
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setWindowName($windowName)
    {
        $this->_requestData[self::$WINDOW_NAME] = $windowName;
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter duplicateRequestCheck
     * @param bool $duplicateRequestCheck
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setDuplicateRequestCheck($duplicateRequestCheck)
    {
        if($duplicateRequestCheck == true)
        {
            $this->_setField(self::$DUPLICATE_REQUEST_CHECK, self::$BOOL_TRUE);
        }
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page paramter customerStatement
     * @param string $customerStatement
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setCustomerStatement($customerStatement)
    {
        $this->_setField(self::$CUSTOMER_STATEMENT, $customerStatement);
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter orderReference
     * @param string $orderReference
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setOrderReference($orderReference)
    {
        $this->_setField(self::$ORDER_REFERENCE, $orderReference);
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page paramter autoDeposit
     * @param string $autoDeposit
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setAutoDeposit($autoDeposit)
    {
        if($autoDeposit == true)
        {
            $this->_setField(self::$AUTO_DEPOSIT, self::$BOOL_TRUE);
        }
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter maxRetries
     * @param string $maxRetries
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setMaxRetries($maxRetries)
    {
        $maxRetries = intval($maxRetries);
        if($maxRetries>=0)
        {
            $this->_setField(self::$MAX_RETRIES, $maxRetries);
        }
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter orderNumber
     * @param string $orderNumber
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setOrderNumber($orderNumber)
    {
        $this->_setField(self::$ORDER_NUMBER, $orderNumber);
        return $this;
    }

    /**
     * setter for the Wirecard Checkout Page parameter confirmMail
     * @param string $confirmMail
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setConfirmMail($confirmMail)
    {
        $this->_setField(self::$CONFIRM_MAIL, $confirmMail);
        return $this;
    }

    /**
     * setter for dataStorage reference data
     * @param type $orderIdent
     * @param type $storageId
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    public function setStorageReference($orderIdent, $storageId)
    {
        $this->_setField(self::$ORDER_IDENT, $orderIdent);
        $this->_setField(self::$STORAGE_ID, $storageId);
        return $this;
    }

    /**
     * adds given consumerData to Wirecard Checkout Page request
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Request_Initiation
     */
    private function _addConsumerData(WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        foreach($consumerData->getData() AS $key => $value)
        {
            $this->_setField($key, $value);
        }
        return $this;
    }

    /**
     * initiate an Wirecard Checkout Page payment.
     * @param string $amount
     * @param string $currency
     * @param string $paymentType
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function initiate($amount, $currency, $paymentType, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData)
    {
        $this->_setField(self::$AMOUNT, $amount);
        $this->_setField(self::$CURRENCY, $currency);
        $this->_setField(self::$PAYMENT_TYPE, $paymentType);
        $this->_setField(self::$ORDER_DESCRIPTION, $orderDescription);
        $this->_setField(self::$SUCCESS_URL, $successUrl);
        $this->_setField(self::$CANCEL_URL, $cancelUrl);
        $this->_setField(self::$FAILURE_URL, $failureUrl);
        $this->_setField(self::$PENDING_URL, $pendingUrl);
        $this->_setField(self::$SERVICE_URL, $serviceUrl);
        $this->_addConsumerData($consumerData);
        $result = $this->_send();
        return new WirecardCEE_Client_QPay_Response_Initiation($result);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype SELECT
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function select($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::SELECT, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype CCARD
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function ccard($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::CCARD, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype CCARD-MOTO
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function ccardMoto($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::CCARD_MOTO, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype MAESTRO
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function maestro($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::MAESTRO, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype EPS
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @param string $financialInstitution
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function eps($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData, $financialInstitution)
    {
        $this->setFinancialInstitution($financialInstitution);
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::EPS, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype IDEAL
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @param string $financialInstitution
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function ideal($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData, $financialInstitution)
    {
        $this->setFinancialInstitution($financialInstitution);
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::IDL, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype PAYBOX
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function paybox($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::PBX, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype ELV
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function elv($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::ELV, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype QUICK
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function quick($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::QUICK, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype C2P
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function click2Pay($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::C2P, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype GIROPAY
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function giropay($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::GIROPAY, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype PAYPAL
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function paypal($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::PAYPAL, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype SOFORTUEBERWEISUNG
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function sofortueberweisung($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::SOFORTUEBERWEISUNG, $orderDescription, $successUrl, $cancelUrl, $failureUrl,$pendingUrl,  $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype BMC
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function bancontactMisterCash($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::BMC, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype INVOICE
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function invoice($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::INVOICE, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype P24
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function przelewy24($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::P24, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype MONETA
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function moneta($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::MONETA, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype POLI
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function poli($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::POLI, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype POLI
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function instantbank($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::INSTANTBANK, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype POLI
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function ekonto($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::EKONTO, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype mpass
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function mpass($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::EKONTO, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype Skrill Direct
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function skrilldirect($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::EKONTO, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }

    /**
     * initiate an Wirecard Checkout Page payment with preset paymenttype Skrill Digital Wallet
     * @param string $amount
     * @param string $currency
     * @param string $orderDescription
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $failurerUrl
     * @param string $pendingUrl
     * @param string $serviceUrl
     * @param WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData
     * @return WirecardCEE_Client_QPay_Response_Initiation
     */
    public function skrillwallet($amount, $currency, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, WirecardCEE_Client_QPay_Request_Initiation_ConsumerData $consumerData)
    {
        return $this->initiate($amount, $currency, WirecardCEE_Client_QPay_Request_Initiation_PaymentType::EKONTO, $orderDescription, $successUrl, $cancelUrl, $failureUrl, $pendingUrl, $serviceUrl, $consumerData);
    }


    /**
     * magic method for setting request parameters.
     * may be used for additional parameters
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value)
    {
        $this->_setField($name, $value);
    }

    protected function _getRequestUrl()
    {
        return WirecardCEE_Client_Configuration::loadConfiguration()->getFrontendUrl().'/init';
    }
}