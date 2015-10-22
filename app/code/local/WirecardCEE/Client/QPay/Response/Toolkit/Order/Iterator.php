<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln. 
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * abstract base class for QPay Client Toolkit Response Iterators.
 */
abstract class WirecardCEE_Client_QPay_Response_Toolkit_Order_Iterator
    implements Iterator
{

    protected   $_position;
    protected   $_objectArray;

    /**
     * @param array $objectArray objects to iterate through
     */
    public function __construct(array $objectArray) 
    {
        $this->_position = 0;
        $this->_objectArray = $objectArray;
    }

    /**
     * resets the current position to 0(first entry)
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * @return the current Object
     */
    public function current()
    {
        return $this->_objectArray[$this->_position];
    }

    /**
     * @return the current position
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * go to the next position
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * checks if position is valid
     */
    public function valid()
    {
        return isset($this->_objectArray[$this->_position]);
    }
}