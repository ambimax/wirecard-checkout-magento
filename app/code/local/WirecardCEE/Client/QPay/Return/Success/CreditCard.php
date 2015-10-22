<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Container for returned creditcard payment data.
 */
final class WirecardCEE_Client_QPay_Return_Success_CreditCard
    extends WirecardCEE_Client_QPay_Return_Success
{
    /**
     * getter for the return parameter anonymousPan
     * @return string
     */
    public function getAnonymousPan()
    {
        return $this->anonymousPan;
    }

    /**
     * getter for the return parameter authenticated
     * @return string
     */
    public function getAuthenticated()
    {
        return $this->authenticated;
    }

    /**
     * getter for the return parameter expiry
     * @return string
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * getter for the return parameter cardholder
     * @return string
     */
    public function getCardholder()
    {
        return $this->cardholder;
    }

    /**
     * getter for the return parameter maskedPan
     * @return string
     */
    public function getMaskedPan()
    {
        return $this->maskedPan;
    }
}