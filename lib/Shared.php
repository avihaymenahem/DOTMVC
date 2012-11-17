<?php

class Shared
{
    /**
     * @static
     */
    public static function setReporting()
    {
        if(defined(DEVELOPMENT_ENVOIRMENT))
        {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        }
        else
        {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', ROOT . DS . 'tmp' . DS . 'logs' . DS . 'error.log');
        }
    }

    /**
     * @static
     * @param $str
     * @return array|string
     */
    public static function stripSlashesDeep($str)
    {
        $str = is_array($str) ? array_map(array('Shared', 'stripSlashesDeep') , $str) : stripslashes($str);
        return $str;
    }

    /**
     * @static
     */
    public static function removeMagicQuotes()
    {
        $_GET = self::stripSlashesDeep($_GET);
        $_POST = self::stripSlashesDeep($_POST);
        $_COOKIE = self::stripSlashesDeep($_COOKIE);
    }

    /**
     * @static
     */
    public static function unregisterGlobals()
    {
        if(ini_get('register_globals'))
        {
            $globalsArray = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach($globalsArray as $value)
            {
                foreach($GLOBALS[$value] as $key => $var)
                {
                    if($var === $GLOBALS[$key])
                    {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }
}