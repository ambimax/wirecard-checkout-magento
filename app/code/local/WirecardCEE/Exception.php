<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/
if(!class_exists('Zend_Exception'))
{
    require_once 'Zend/Exception.php';
}

/**
 * base class for all Wirecard CEE exceptions.
 */
class WirecardCEE_Exception extends Zend_Exception
{
    
}