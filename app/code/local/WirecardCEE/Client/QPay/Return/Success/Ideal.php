<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * Container for returned iDEAL payment data.
 */
final class WirecardCEE_Client_QPay_Return_Success_Ideal
    extends WirecardCEE_Client_QPay_Return_Success
{
    /**
     * getter for the return parameter idealConsumerName
     * @return string
     */
    public function getConsumerName()
    {
        return $this->idealConsumerName;
    }

    /**
     * getter for the return parameter idealConsumerCity
     * @return string
     */
    public function getConsumerCity()
    {
        return $this->idealConsumerCity;
    }

    /**
     * getter for the return parameter idealConsunerAccountNumber
     * @return string
     */
    public function getConsumerAccountNumber()
    {
        return $this->idealConsumerAccountNumber;
    }
}
