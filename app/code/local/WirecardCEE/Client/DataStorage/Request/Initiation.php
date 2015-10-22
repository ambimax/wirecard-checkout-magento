<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/


final class WirecardCEE_Client_DataStorage_Request_Initiation
    extends WirecardCEE_Client_Request_Abstract
{
    protected static $RETURN_URL = 'returnUrl';
    protected static $ORDER_IDENT = 'orderIdent';
    protected static $JAVASCRIPT_SCRIPT_VERSION = 'javascriptScriptVersion';
	protected static $IFRAME_CSS_URL = 'iframeCssUrl';
	protected static $CREDITCARD_SHOW_ISSUE_DATEFIELD = 'creditcardShowIssueDateField';
	protected static $CREDITCARD_SHOW_ISSUE_NUMBERFIELD = 'creditcardShowIssueNumberField';
	protected static $CREDITCARD_SHOW_CARDHOLDER_NAMEFIELD = 'creditcardShowCardholderNameField';
	protected static $CREDITCARD_SHOW_CVC_FIELD = 'creditcardShowCvcField';

    protected $_fingerprintOrderType = 1;

    public function __construct($customerId, $shopId, $language, $returnUrl, $secret)
    {
        $this->_setField(self::$RETURN_URL, $returnUrl);
        //for fingerprint calculation there must be at least an empty javascriptScriptVersion
        $this->_setField(self::$JAVASCRIPT_SCRIPT_VERSION, '');
        $this->_setSecret($secret);
        parent::__construct($customerId, $shopId, $language);
    }

    /**
     * setter for parameter javascriptScriptVersion
     * @param type $javascriptVersion
     */
    public function setJavascriptScriptVersion($javascriptScriptVersion)
    {
        $this->_setField(self::$JAVASCRIPT_SCRIPT_VERSION, $javascriptScriptVersion);
    }

    /**
	 * setter for parameter iframeCssUrl
	 * @param $iframeCssUrl
	 */
	public function setIframeCssUrl($iframeCssUrl)
	{
		$this->_setField(self::$IFRAME_CSS_URL, $iframeCssUrl);
	}

	/**
	 * setter for parameter showIssueDateFields
	 * @param $showIssueDateField
	 */
	public function setCreditCardShowIssueDateField($showIssueDateField)
	{
		$this->_setField(self::$CREDITCARD_SHOW_ISSUE_DATEFIELD, $showIssueDateField ? 'true' : 'false');
	}

	/**
	 * setter for parameter showIssueNumberField
	 * @param $showIssueNumberField
	 */
	public function setCreditCardShowIssueNumberField($showIssueNumberField)
	{
		$this->_setField(self::$CREDITCARD_SHOW_ISSUE_NUMBERFIELD, $showIssueNumberField ? 'true' : 'false');
	}

	/**
	 * setter for parameter showCardholderField
	 * @param $showCardholderField
	 */
	public function setCreditCardCardholderNameField($showCardholderField)
	{
		$this->_setField(self::$CREDITCARD_SHOW_CARDHOLDER_NAMEFIELD, $showCardholderField ? 'true' : 'false');
	}

	/**
	 * setter for parameter showCvcField
	 * @param $showCvcField
	 */
	public function setCreditCardShowCvcField($showCvcField)
	{
		$this->_setField(self::$CREDITCARD_SHOW_CVC_FIELD, $showCvcField ? 'true' : 'false');
	}

	/**
     * @param string $orderIdent
     * @return WirecardCEE_Client_DataStorage_Response_Initiation
     */
    public function initiate($orderIdent)
    {
        $this->_setField(self::$ORDER_IDENT, $orderIdent);
        $this->_fingerprintOrder = Array(self::$CUSTOMER_ID, self::$SHOP_ID, self::$ORDER_IDENT, self::$RETURN_URL, self::$LANGUAGE, self::$JAVASCRIPT_SCRIPT_VERSION, self::$SECRET);
        $result = $this->_send();
        return new WirecardCEE_Client_DataStorage_Response_Initiation($result);
    }

    protected function _getRequestUrl()
    {
        return WirecardCEE_Client_Configuration::loadConfiguration()->getDataStorageUrl().'/init';
    }
}