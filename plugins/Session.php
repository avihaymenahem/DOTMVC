<?php

class Session
{
    public static function init()
    {
        @session_start();
    }

    /**
     * @static
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = serialize($value);
    }

    /**
     * @static
     * @param $key
     * @return mixed|null
     */
    public static function get($key)
    {
        if(isset($_SESSION[$key]))
        {
            return unserialize($_SESSION[$key]);
        }

        return null;
    }

    /**
     * @static
     * @param $key
     * @return null
     */
    public static function delete($key)
    {
        if(isset($_SESSION[$key]))
        {
            unset($_SESSION[$key]);
        }
        return null;
    }

    /**
     * @static
     */
    public static function destroy()
    {
        unset($_SESSION);
        session_destroy();
    }
}