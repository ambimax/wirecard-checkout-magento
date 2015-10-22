<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Response class for QPAY Initiations
 */
final class WirecardCEE_Client_QPay_Response_Initiation
    extends WirecardCEE_Client_QPay_Response_Abstract
{
    const STATE_SUCCESS = 0;
    const STATE_FAILURE = 1;

    protected static $REDIRECT_URL = 'redirectUrl';

    /**
     * getter for the Response status
     * values: 0 ... success
     *         1 ... failure
     * @return int
     */
    public function getStatus()
    {
        //if we have got a redirectUrl the initiation has been successful
        if($this->_getField(self::$REDIRECT_URL))
        {
            return self::STATE_SUCCESS;
        }
        else
        {
            return self::STATE_FAILURE;
        }
    }

    /**
     * getter for the returned redirect url
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_getField(self::$REDIRECT_URL);
    }

}