<?php

class WirecardCEE_Client_Configuration
{
    private $_dataStorageUrl = null;
    private $_frontendUrl    = null;
    private $_backendUrl     = null;

    private static $_configuration;

    const DATASTORAGE_URL = 'DATA_STORAGE_URL';
    const FRONTEND_URL = 'FRONTEND_URL';
    const BACKEND_URL = 'BACKEND_URL';

    /**
     * private constructor to prevent direct initiation
     */
    private function __construct($configFile)
    {
        if(is_array($configFile))
        {
            $iniArray = $configFile;
        }
        else
        {
            if (defined('__DIR__')) {
                $dirpath = realpath(__DIR__);
            }
            else
            {
                $dirpath = realpath(dirname(__FILE__));
            }
            $filename = realpath($dirpath . DIRECTORY_SEPARATOR . $configFile);
            $iniArray = parse_ini_file($filename, true);
        }
        if(isset($iniArray[self::DATASTORAGE_URL]))
        {
            $this->_dataStorageUrl = (string)$iniArray[self::DATASTORAGE_URL];
        }
        if(isset($iniArray[self::FRONTEND_URL]))
        {
            $this->_frontendUrl = (string)$iniArray[self::FRONTEND_URL];
        }
        if(isset($iniArray[self::BACKEND_URL]))
        {
            $this->_backendUrl = (string)$iniArray[self::BACKEND_URL];
        }
    }

    /**
     *
     * @param string $configFile optional - relative path from this class to configuration File
     * @return WirecardCEE_Client_Configuration
     */
    public static function loadConfiguration($configFile = null)
    {
        if(!self::$_configuration || $configFile != null)
        {
            //if no configFile is set use default config.ini
            if($configFile == null)
            {
                $configFile = 'config.ini';
            }
            self::$_configuration = new self($configFile);
        }
        return self::$_configuration;
    }

    public static function createConfigurationFromArray(Array $config)
    {
        self::$_configuration = new self($config);
        return self::$_configuration;
    }

    public function getDataStorageUrl()
    {
        return $this->_dataStorageUrl;
    }

    public function getFrontendUrl()
    {
        return $this->_frontendUrl;
    }

    public function getBackendUrl()
    {
        return $this->_backendUrl;
    }
}