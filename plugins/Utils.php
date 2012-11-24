<?php

class Utils
{
    /**
     * @static
     * @param int $length
     * @return string
     */
    public static function randString($length = 8)
    {
        $string = null;
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

        for($x=0; $x<$length; $x++)
        {
            $string.= $characters[mt_rand(0,strlen($characters))];
        }

        return $string;
    }

    /**
     * @static
     * @param $time
     * @param null $minus
     * @return string
     */
    public static function timeAgo($time, $minus = null)
    {
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");
        $now = time();

        $difference = $time - $now;
        if($minus != null) { $difference = $now - $time; }

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if($difference != 1) {
            $periods[$j].= "s";
        }
        return "$difference $periods[$j]";
    }

    /**
     * @static
     * @param $email
     * @return int
     */
    public static function isValidEmail($email)
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        return preg_match($regex, $email);
    }

    /**
     * @static
     * @param $imagePath
     * @return string
     */
    public static function getImage($imagePath)
    {
        return BASE_URL . 'public' . DS . 'static' . DS . 'img' . DS . $imagePath;
    }
}