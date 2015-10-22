<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * iDEAL Payment-object returned by an {@link WirecardCEE_Client_QPay_Request_Toolkit::getOrderDetails()} operation.
 */
final class WirecardCEE_Client_QPay_Response_Toolkit_Order_Payment_Ideal
    extends WirecardCEE_Client_QPay_Response_Toolkit_Order_Payment
{
    private static $CONSUMER_NAME = 'idealConsumerName';
    private static $CONSUMER_CITY = 'idealConsumerCity';
    private static $CONSUMER_ACCOUNT_NUMBER = 'idealConsumerAccountNumber';


    /**
     * getter for iDEAL consumer Name
     * @return string
     */
    public function getConsumerName()
    {
        return $this->_getField(self::$CONSUMER_NAME);
    }

    /**
     * getter for iDEAL consumer City
     * @return string
     */
    public function getConsumerCity()
    {
        return $this->_getField(self::$CONSUMER_CITY);
    }

    /**
     * getter for iDEAL consumer account-number
     * @return string
     */
    public function getConsumerAccountNumber()
    {
        return $this->_getField(self::$CONSUMER_ACCOUNT_NUMBER);
    }

}
