<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

class WirecardCEE_SerialApi
{
    /**
     * Encode the mixed[] $valueToEncode into the SerialAPI format
     *
     * NOTE: only Strings can be handled. So for every object the __toString will be called.
     *
     * @throws Zend_Exception if valueToEncode is not an array.
     * @param  mixed[] $valueToEncode
     * @return string SerialAPI encoded Array
     */
    public static function encode($valueToEncode)
    {
        if(is_array($valueToEncode))
        {
            $serializedString = '';
            foreach($valueToEncode AS $key => $value)
            {
                $serializedString = self::_addEntryEncode($key, $value, $serializedString);
            }
            return $serializedString;
        }
        else
        {
            throw new WirecardCEE_Exception('Invalid type for WirecardCEE_SerialApi::encode. Array must be given.');
        }
    }

    /**
     * Adds an key/value pair to the serializedString
     *
     * @param string key representing the entry
     * @param mixed|mixed[] value for key entry
     * @param string serialized String
     */
    protected static function _addEntryEncode($key, $value, $serializedString = '')
    {
        if(is_array($value))
        {
            $entryValue = Array();
            $entryKey = '';
            $nextEntryKey = '';
            $nextEntryValue = '';
            foreach($value AS $subKey => $subValue)
            {
                if(is_int($subKey))
                {
                    $subKey++;
                    if(!is_array($subValue))
                    {
                        if($entryKey == '')
                        {

                            if(is_numeric(substr(strrchr($key, '.'), 1)))
                            {
                                $entryKey = $key.'.'.$subKey;
                            }
                            else
                            {
                                $entryKey = $key;
                            }
                        }
                        $entryValue[] = $subValue;
                        //next loop
                        continue;
                    }
                    else
                    {
                        if(!empty($entryValue))
                        {
                            $serializedString = self::_addLastEntryArrayEncode($entryKey, $entryValue, $serializedString);
                            $entryValue = '';
                            $entryKey = '';
                        }
                    }
                }
                if(empty($entryValue))
                {
                    $serializedString = self::_addEntryEncode($key.'.'.$subKey, $subValue, $serializedString);
                }
                else
                {
                    $nextEntryKey = $key.'.'.$subKey;
                    $nextEntryValue = $subValue;
                }
            }
            if(!empty($entryValue))
            {
                $serializedString = self::_addLastEntryArrayEncode($entryKey, $entryValue, $serializedString);
                $entryValue = '';
                $entryKey = '';
                if($nextEntryKey != '' && $nextEntryValue != '')
                {
                    $serializedString = self::_addEntryEncode($nextEntryKey, $nextEntryValue, $serializedString);
                    $nextEntryKey = '';
                    $nextEntryValue = '';
                }
            }
        }
        else
        {
            if($serializedString != '')
            {
                $serializedString .= '&';
            }
            if(is_int($key))
            {
                $key++;
            }
            $serializedString .= urlencode((string)$key).'='.urlencode((string)$value);
        }
        return $serializedString;
    }

    protected static function _addLastEntryArrayEncode($key, Array $values, $serializedString)
    {
        $valueString = '';
        foreach($values AS $value)
        {
            if($valueString == '')
            {
                $valueString = urlencode((string)$value);
            }
            else
            {
                $valueString .= ','.urlencode((string)$value);
            }
        }
        if($serializedString == '')
        {
            $serializedString = urlencode((string)$key).'='.$valueString;
        }
        else
        {
            $serializedString .= '&'.urlencode((string)$key).'='.$valueString;
        }
        return $serializedString;
    }

    public static function decode($encodedValue)
    {
        $decodedValue = Array();
        $keyValueStrings = explode('&', $encodedValue);
        foreach($keyValueStrings AS $entry)
        {
            $decodedValue = self::_addEntryDecode($entry, $decodedValue);
        }
        return $decodedValue;
    }

    protected static function _addEntryDecode($entry, $decodeValue)
    {
        $entryArray = explode('=', $entry);
        if(!is_array($entryArray) || count($entryArray) < 2)
        {
            //ignore keys only
            return $decodeValue;
        }
        else if(count($entryArray) == 2)
        {
            $keyArray = explode('.', $entryArray[0]);
            if(is_array($keyArray) && count($keyArray) > 1)
            {
                $position =& $decodeValue;
                foreach($keyArray AS $keyName)
                {
                    if($keyName == intval($keyName))
                    {
                        $keyName--;
                    }
                    if(!isset($position[$keyName]))
                    {
                        $position[$keyName] = Array();
                    }
                    $position =& $position[$keyName];
                }
                $position = self::_decodeValueArray($entryArray[1]);
            }
            else
            {
                if($entryArray[0] == intval($entryArray[0]))
                {
                    $entryArray[0]--;
                }
                $decodeValue[urldecode($entryArray[0])] = self::_decodeValueArray($entryArray[1]);
            }
            return $decodeValue;
        }
        else
        {
            throw new WirecardCEE_Exception('Invalid format for WirecardCEE_SerialApi::decode. Expecting key=value pairs');
        }
    }

    protected static function _decodeValueArray($value)
    {
        $values = explode(',', $value);
        if(is_array($values) && count($values) > 1)
        {
            $entries = Array();
            foreach($values AS $entry)
            {
                $entries[] = urldecode($entry);
            }
            return $entries;
        }
        else
        {
            return urldecode($value);
        }
    }
}
