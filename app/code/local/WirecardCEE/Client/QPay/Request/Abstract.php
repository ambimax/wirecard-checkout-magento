<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

abstract class WirecardCEE_Client_QPay_Request_Abstract
    extends WirecardCEE_Client_Request_Abstract
{
    protected $_secret;
    protected $_fingerprintOrderString;
    protected $_httpClient;
    protected $_fingerprintOrderType = '';
    protected $_fingerprintString;
    protected $_fingerprintOrder = Array();
    protected $_requestData;

    protected static $REQUEST_FINGERPRINT = 'requestFingerprint';
    protected static $AMOUNT = 'amount';
    protected static $CURRENCY = 'currency';
    protected static $ORDER_DESCRIPTION = 'orderDescription';
    protected static $AUTO_DEPOSIT = 'autoDeposit';
    protected static $ORDER_NUMBER = 'orderNumber';
    protected static $PLUGIN_VERSION = 'pluginVersion';
    protected static $FINGERPRINT_TYPE_DYNAMIC = 0;
    protected static $FINGERPRINT_TYPE_FIXED = 1;

    protected static $BOOL_TRUE = 'yes';
    protected static $BOOL_FALSE = 'no';

    protected static $FRAMEWORK_NAME = 'Zend Framework';

    protected $_requestUrl = '';

    /**
     * abstract constructor for WirecardCEE_Client_QPay_Request_Initiation objects.
     * @param string $customerId
     * @param string $secret
     * @param string $language
     * @param string $pluginVersion
     * @param string $shopId
     * @param string[]|null $config
     */
    public function __construct($customerId, $shopId, $secret, $language, $pluginVersion)
    {
        $this->_setField(self::$PLUGIN_VERSION, $pluginVersion);
        $this->_setSecret($secret);
        parent::__construct($customerId, $shopId, $language);
    }

    /**
     * Getter for QPay Client Library Versionstring
     * @access private
     * @return String
     */
    protected static function _getQPayClientVersionString()
    {
        return self::$LIBRARY_NAME . ' ' . self::$LIBRARY_VERSION;
    }

    /**
     * Getter for Zend Framework Versionstring
     * @access private
     * @return string
     */
    protected static function _getZendFrameworkVersionString()
    {
        if(!class_exists('Zend_Version', false))
        {
            require_once('Zend/Version.php');
        }
        return self::$FRAMEWORK_NAME . ' ' . Zend_Version::VERSION;
    }

    /**
     * generates an base64 encoded pluginVersion string from the given shop- plugin- and library-versions
     * QPAY Client Libary and Zend Framework Version will be added automatically
     * @param string $shopName
     * @param string $shopVersion
     * @param string $pluginName
     * @param string $pluginVersion
     * @param array|null $libraries
     * @return string base64 encoded pluginVersion
     */
    public static function generatePluginVersion($shopName, $shopVersion , $pluginName, $pluginVersion, $libraries = null)
    {
        $libraryString = self::_getQPayClientVersionString();
        $libraryString .= ', ' . self::_getZendFrameworkVersionString();
        if(is_array($libraries))
        {
            foreach($libraries AS $libName => $libVersion)
            {
                $libraryString .= ',' . strval($libName) . ' ' . strval($libVersion);
            }
        }
        $version = base64_encode(strval($shopName) . ';' . strval($shopVersion) . ';' . $libraryString . ';' . strval($pluginName) . ';' . strval($pluginVersion));

        return $version;
    }

}
