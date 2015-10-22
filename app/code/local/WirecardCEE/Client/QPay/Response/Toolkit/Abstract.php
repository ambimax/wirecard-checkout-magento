<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * abstract baseclass for toolkit response objects
 */
abstract class WirecardCEE_Client_QPay_Response_Toolkit_Abstract
    extends WirecardCEE_Client_QPay_Response_Abstract
{
    private static $STATUS = 'status';
    private static $PAY_SYS_MESSAGE = 'paySysMessage';
    private static $ERROR_CODE = 'errorCode';


    /**
     * getter for the toolkit operation status
     * @return string
     */
    public function getStatus()
    {
        return $this->_getField(self::$STATUS);
    }
}