<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Response class for toolkit operation refund
 */
final class WirecardCEE_Client_QPay_Response_Toolkit_Refund
    extends WirecardCEE_Client_QPay_Response_Toolkit_Abstract
{
    private static $CREDIT_NUMBER = 'creditNumber';

    /**
     * getter for the returned credit number
     * @return string
     */
    public function getCreditNumber()
    {
        return $this->_getField(self::$CREDIT_NUMBER);
    }
}
