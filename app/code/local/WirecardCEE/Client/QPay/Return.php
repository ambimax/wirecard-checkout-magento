<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Factory method for returned params validators
 */
final class WirecardCEE_Client_QPay_Return
{
    const STATE_SUCCESS = 'SUCCESS';
    const STATE_PENDING = 'PENDING';
    const STATE_CANCEL  = 'CANCEL';
    const STATE_FAILURE = 'FAILURE';

    /**
     * no initiation allowed.
     */
    private function __construct()
    {

    }

    /**
     * creates an Return instance (Cancel, Failure, Success...)
     * @param array $return - returned post data
     * @param type $secret - QPAY secret
     * @return WirecardCEE_Client_QPay_Return_Abstract
     */
    public static function createReturnInstance($return, $secret)
    {
        if(!is_array($return))
        {
            $return = WirecardCEE_SerialApi::decode($return);
        }

        if(array_key_exists('paymentState', $return))
        {
            return self::_getInstance($return, $secret);
        }
        else
        {
            throw new WirecardCEE_Client_QPay_Exception('Invalid response from QPAY. Paymentstate is missing.');
        }
    }

    /**
     * validate the given Response
     */
    private static function _getInstance($return, $secret)
    {
        switch(strtoupper($return['paymentState']))
        {
            case self::STATE_SUCCESS:
                return self::_getSuccessInstance($return, $secret);
                break;
            case self::STATE_PENDING:
                return new WirecardCEE_Client_QPay_Return_Pending($return, $secret);
                break;
            case self::STATE_CANCEL:
                return new WirecardCEE_Client_QPay_Return_Cancel($return);
                break;
            case self::STATE_FAILURE:
                return new WirecardCEE_Client_QPay_Return_Failure($return);
                break;
            default:
                throw new WirecardCEE_Client_QPay_Exception('Invalid response from QPAY. Unexpected paymentState: ' . $return['paymentState']);
                break;
        }
    }

    /**
     * getter for the correct qpay success return instance
     * @param string[] $return
     * @param string $secret
     * @access private
     * @return WirecardCEE_Client_QPay_Return_Success_CreditCard
     */
    private static function _getSuccessInstance($return, $secret)
    {
        if(!array_key_exists('paymentType', $return))
        {
            throw new WirecardCEE_Client_QPay_Exception('Invalid response from QPAY. Paymenttype is missing.');
        }
        switch(strtoupper($return['paymentType']))
        {
            case 'CCARD':
            case 'CCARD-MOTO':
            case 'MAESTRO':
                return new WirecardCEE_Client_QPay_Return_Success_CreditCard($return, $secret);
                break;
            case 'PAYPAL':
                return new WirecardCEE_Client_QPay_Return_Success_PayPal($return, $secret);
                break;
            case 'SOFORTUEBERWEISUNG':
                return new WirecardCEE_Client_QPay_Return_Success_Sofortueberweisung($return, $secret);
                break;
            case 'IDL':
                return new WirecardCEE_Client_QPay_Return_Success_Ideal($return, $secret);
                break;
            case 'ELV':
                return new WirecardCEE_Client_QPay_Return_Success_Elv($return, $secret);
                break;
            default:
                return new WirecardCEE_Client_QPay_Return_Success($return, $secret);
                break;
        }
    }

    /**
     * generator for qpay confirm response strings.
     * the response-string must be returned to QPAY in confirmation process.
     * @param string $messages
     * @param bool $inCommentTag
     * @return string
     */
    public static function generateConfirmResponseString($messages = null, $inCommentTag = false)
    {
        $template = '<QPAY-CONFIRMATION-RESPONSE %message% result="%status%" />';
        if(empty($messages))
        {
            $returnValue = str_replace('%status%', 'OK', $template);
            $returnValue = str_replace('%message% ', '', $returnValue);
        }
        else
        {
            $returnValue = str_replace('%status%', 'NOK', $template);
            $returnValue = str_replace('%message%', 'message="' . strval($messages) . '"', $returnValue);
        }
        if($inCommentTag)
        {
            $returnValue = '<!--'.$returnValue.'-->';
        }
        return $returnValue;
    }
}