<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * container for failure return data.
 */
final class WirecardCEE_Client_QPay_Return_Failure
    extends WirecardCEE_Client_QPay_Return_Abstract
{
    protected $_state = 'FAILURE';

    protected $_errors = Array();

    protected static $ERRORS = 'errors';
    protected static $ERROR = 'error';
    protected static $ERROR_ERROR_CODE = 'errorCode';
    protected static $ERROR_MESSAGE = 'message';
    protected static $ERROR_CONSUMER_MESSAGE = 'consumerMessage';
    protected static $ERROR_PAY_SYS_MESSAGE = 'paySysMessage';

    public function getNumberOfErrors()
    {
        return $this->__get(self::$ERRORS);
    }

    public function getErrors()
    {
        if(empty($this->_errors))
        {
            $errors = $this->__get(self::$ERROR);
            $i = 0;
            $errorList = Array();
            foreach($errors AS $error)
            {
                $errorList[$i] = new WirecardCEE_Error($error[self::$ERROR_ERROR_CODE], $error[self::$ERROR_MESSAGE]);
                if(isset($error[self::$ERROR_CONSUMER_MESSAGE]))
                {
                    $errorList[$i]->setConsumerMessage($error[self::$ERROR_CONSUMER_MESSAGE]);
                }
                if(isset($error[self::$ERROR_PAY_SYS_MESSAGE]))
                {
                    $errorList[$i]->setPaySysMessage($error[self::$ERROR_PAY_SYS_MESSAGE]);
                }
                $i++;
            }
            $this->_errors = $errorList;
        }
        return $this->_errors;
    }
}