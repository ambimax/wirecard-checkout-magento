<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * abstract base-class for financial Toolkit objects
 * e.g. {@link WirecardCEE_Client_QPay_Response_Toolkit_Order}
 */
abstract class WirecardCEE_Client_QPay_Response_Toolkit_FinancialObject
{
    protected $_data = Array();
    
    protected static $DATETIME_FORMAT = 'm.d.Y H:i:s';

    /**
     * getter for given field
     * @access private
     * @param string $name
     * @return string
     */
    protected function _getField($name)
    {
        if(array_key_exists($name, $this->_data))
        {
            return $this->_data[$name];
        }
        else
        {
            return false;
        }
    }
}