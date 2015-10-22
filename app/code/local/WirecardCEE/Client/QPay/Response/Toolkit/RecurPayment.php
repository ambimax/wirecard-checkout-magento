<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Response class for toolkit operation recurpayment
 */
final class WirecardCEE_Client_QPay_Response_Toolkit_RecurPayment
    extends WirecardCEE_Client_QPay_Response_Toolkit_Abstract
{
    private static $ORDER_NUMBER = 'orderNumber';

    /**
     * getter for the returned order number
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->_getField(self::$ORDER_NUMBER);
    }
}
