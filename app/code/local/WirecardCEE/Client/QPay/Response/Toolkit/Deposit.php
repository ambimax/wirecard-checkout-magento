<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Response class for toolkit operation deposit
 */
final class WirecardCEE_Client_QPay_Response_Toolkit_Deposit
    extends WirecardCEE_Client_QPay_Response_Toolkit_Abstract
{
    private static $PAYMENT_NUMBER = 'paymentNumber';

    /**
     * getter for the returned paymentNumber
     * @return string
     */
    public function getPaymentNumber()
    {
        return $this->_getField(self::$PAYMENT_NUMBER);
    }
}
