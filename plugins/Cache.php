<?php

class Cache
{
    private static $instance;
    private $folderPath;

    /**
     * @static
     * @return Cache
     */
    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new Cache();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->folderPath = ROOT . DS . 'tmp' . DS . 'cache' . DS;
    }

    /**
     * @param $fileName
     * @return mixed|null
     */
    public function get($fileName)
    {
        $fileName = md5($fileName);
        $fileName = $this->folderPath . $fileName;
        if (file_exists($fileName))
        {
            $handle = fopen($fileName, 'rb');
            $variable = fread($handle, filesize($fileName));
            fclose($handle);
            return unserialize($variable);
        } else {
            return null;
        }
    }

    /**
     * @param $fileName
     * @param $variable
     */
    public function set($fileName, $variable)
    {
        $fileName = md5($fileName);
        $fileName = $this->folderPath . $fileName;
        $handle = fopen($fileName, 'a');
        fwrite($handle, serialize($variable));
        fclose($handle);
    }
}