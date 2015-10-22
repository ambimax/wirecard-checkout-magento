<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * abstract base class for WirecardCEE Response objects
 */
abstract class WirecardCEE_Client_Response_Abstract
{
    protected $_response = Array();


    protected static $ERRORS = 'errors';
    protected static $ERROR = 'error';

    protected static $ERROR_ERRORCODE = 'errorCode';
    protected static $ERROR_MESSAGE = 'message';
    protected static $ERROR_CONSUMER_MESSAGE = 'consumerMessage';
    protected static $ERROR_PAYSYS_MESSAGE = 'paySysMessage';

    protected $_errors = Array();

    /**
     * base ctor for Response objects
     * @param Zend_Http_Response $response
     */
    public function __construct($response)
    {
        if($response instanceof Zend_Http_Response)
        {
            $this->_response = WirecardCEE_SerialApi::decode($response->getBody());
        }
        else if(is_array($response))
        {
            $this->_response = $response;
        }
        else
        {
            throw new WirecardCEE_Exception('Invalid response from WirecardCEE');
        }
    }

    /**
     * returns the status of the result
     * @return mixed
     */
    abstract public function getStatus();

    /**
     * getter for given field
     * @access private
     * @param string $name
     * @return string
     */
    protected function _getField($name)
    {
        if(array_key_exists($name, $this->_response))
        {
            return $this->_response[$name];
        }
        else
        {
            return null;
        }
    }

    public function getNumberOfErrors()
    {
        return $this->_getField(self::$ERRORS);
    }

    /**
     * getter for list of errors that occured
     * @return WirecardCEE_Error[]
     */
    public function getErrors()
    {
        $errors = Array();
        if(empty($this->_errors))
        {
            if(is_array($this->_getField(self::$ERROR)))
            {
                foreach($this->_getField(self::$ERROR) AS $error)
                {
                    $errorCode = isset($error[self::$ERROR_ERRORCODE]) ? $error[self::$ERROR_ERRORCODE] : '';
                    $message = isset($error[self::$ERROR_MESSAGE]) ? $error[self::$ERROR_MESSAGE] : '';
                    $consumerMessage = isset($error[self::$ERROR_CONSUMER_MESSAGE]) ? $error[self::$ERROR_CONSUMER_MESSAGE] : '';
                    $paySysMessage = isset($error[self::$ERROR_PAYSYS_MESSAGE]) ? $error[self::$ERROR_PAYSYS_MESSAGE] : '';
                    $error = new WirecardCEE_Error($errorCode, $message);
                    $error->setConsumerMessage($consumerMessage);
                    $error->setPaySysMessage($paySysMessage);
                    $errors[] = $error;
                }
            }
            $this->_errors = $errors;
        }
        return $this->_errors;
    }
}