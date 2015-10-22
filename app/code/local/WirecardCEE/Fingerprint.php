<?php

class WirecardCEE_Fingerprint
{
    const HASH_ALGORITHM_MD5 = 'md5';
    const HASH_ALGORITHM_SHA512 = 'sha512';

    protected static $_HASH_ALGORITHM = self::HASH_ALGORITHM_SHA512;
    protected static $_STRIP_SLASHES = false;

    /**
     * use stripslashes for fingerprint generate methods
     * @param boolean $strip
     */
    public static function stripSlashes($strip)
    {
        self::$_STRIP_SLASHES = filter_var($strip, FILTER_VALIDATE_BOOLEAN);
    }

    public static function setHashAlgorithm($hashAlgorithm)
    {
        self::$_HASH_ALGORITHM = (string)$hashAlgorithm;
    }

    /**
     * generates an Fingerprint-string
     * @param array $values
     * @param array $fingerprintOrder
     */
    public static function generate(Array $values, Array $fingerprintOrder)
    {
        $hash = hash_init(self::$_HASH_ALGORITHM);
        foreach($fingerprintOrder AS $key)
        {
            $key = strval($key);
            if(array_key_exists($key, $values))
            {
                hash_update($hash, self::_prepareFingerprintValue($values[$key]));
            }
            else
            {
                throw new WirecardCEE_Exception('Value for key ' . strtoupper($key) . ' not found in values array.');
            }
        }
        return hash_final($hash);
    }

    public static function compare(Array $values, $fingerprintOrder, $compareFingerprint)
    {
        $calcFingerprint = self::generate($values, $fingerprintOrder);
        if(strcasecmp($calcFingerprint, $compareFingerprint) == 0)
        {
            return true;
        }
        return false;
    }

    protected static function _prepareFingerprintValue($value)
    {
        if(self::$_STRIP_SLASHES)
        {
            return stripslashes($value);
        }
        return $value;
    }

    public static function fingerprintOrderToString(Array $array)
    {
        $string = '';
        foreach($array AS $entry)
        {
            if($string != '')
            {
                $string .= ',';
            }
            $string .= strval($entry);
        }
        return $string;
    }
}