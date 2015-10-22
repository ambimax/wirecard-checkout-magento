<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * class to read current dataStorage content
 * because the merchant NEVER should read plain payment-data the information will be anonymized
 */
final class WirecardCEE_Client_DataStorage_Request_Read
    extends WirecardCEE_Client_Request_Abstract
{
    protected static $STORAGE_ID = 'storageId';
    protected $_fingerprintOrderType = 1;

    public function __construct($customerId, $shopId, $secret)
    {
        $language = 'en';
        $this->_setSecret($secret);
        parent::__construct($customerId, $shopId, $language);
    }

    public function read($storageId)
    {
        $this->_setField(self::$STORAGE_ID, $storageId);
        $this->_fingerprintOrder = Array(self::$CUSTOMER_ID, self::$SHOP_ID, self::$STORAGE_ID, self::$SECRET);
        $result = $this->_send();
        return new WirecardCEE_Client_DataStorage_Response_Read($result);
    }

    protected function _getRequestUrl()
    {
        return WirecardCEE_Client_Configuration::loadConfiguration()->getDataStorageUrl().'/read';
    }
}