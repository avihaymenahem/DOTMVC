<?php

class Cache
{
    private static $instance;
    private $cacheDir;
    private $cacheTime;
    private $cacheFilePath;
    private $fileEXT;
    private $fileName;
    private $controllerPrefix;

    /**
     * @static
     * @return Cache
     */
    public static function getInstance($file, $controller)
    {
        if(!self::$instance)
            self::$instance = new Cache($file, $controller);

        return self::$instance;
    }

    private function __construct($file, $controller)
    {
        $this->cacheTime = defined('CACHE_TIME') ? CACHE_TIME : 1200;
        $this->cacheDir = ROOT . DS . 'public' . DS . 'tmp' . DS . 'cache' . DS;
        $this->fileEXT = '.tmp';
        $this->fileName = $file;
        $this->controllerPrefix = $controller;
        $this->cacheFilePath = $this->cacheDir . md5($this->fileName) . '_' . $this->controllerPrefix . '_' . str_replace(".phtml", "", $this->fileName) . $this->fileEXT;
    }

    public function isExist()
    {
        return file_exists($this->cacheFilePath);
    }

    public function getTemplate()
    {
        if($this->isExist())
        {
            Trace::debug("Cache hit for template {$this->fileName}");
            $content = file_get_contents($this->cacheFilePath);
            return $content;
        }
        else
        {
            return false;
        }
    }

    public function fileIsOld()
    {
        return (time() - filemtime($this->cacheFilePath) > $this->cacheTime) ? true : false;
    }

    public function setTemplate()
    {
        Trace::Debug("Cache miss for {$this->fileName}");
        $content = ob_get_contents();
        $fh = fopen($this->cacheFilePath, 'w');
        fwrite($fh, $content);
        fclose($fh);
        ob_end_clean();
        return $content;
    }
}