<?php

class WirecardCEE_Error
{
    protected $_errorCode = null;
    protected $_message= null;
    protected $_consumerMessage = null;
    protected $_paySysMessage = null;

    public function __construct($errorCode, $message)
    {
        $this->_errorCode = (string)$errorCode;
        $this->_message = (string)$message;
    }
    
    public function getErrorCode()
    {
        return $this->_errorCode;
    }
    
    public function getMessage()
    {
        return $this->_message;
    }
    
    public function setConsumerMessage($consumerMessage)
    {
        $this->_consumerMessage = (string)$consumerMessage;
        return $this;
    }
    
    public function setPaySysMessage($paySysMessage)
    {
        $this->_paySysMessage = (string)$paySysMessage;
        return $this;
    }
    
    public function getConsumerMessage()
    {
        return $this->_consumerMessage;
    }
    
    public function getPaySysMessage()
    {
        return $this->_paySysMessage;
    }
}