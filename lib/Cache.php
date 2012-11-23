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
    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance = new Cache();

        return self::$instance;
    }

    private function __construct()
    {
        $this->cacheTime = defined('CACHE_TIME') ? CACHE_TIME : 1200;
        $this->cacheDir = ROOT . DS . 'public' . DS . 'tmp' . DS . 'cache' . DS;
        $this->fileEXT = '.tmp';
    }

    public function init($file, $controllerPrefix)
    {
        $this->fileName = $file;
        $this->controllerPrefix = $controllerPrefix;
        $this->cacheFilePath = $this->cacheDir . md5($this->fileName) . '_' . $this->controllerPrefix . '_' . $this->fileName . $this->fileEXT;

        $this->getTemplate();
        ob_start();
    }

    public function getTemplate()
    {
        if(file_exists($this->cacheFilePath))
        {
            if(time() - filemtime($this->cacheFilePath) < $this->cacheTime)
            {
                $content = file_get_contents($this->cacheFilePath);
                Trace::debug("Cache hit for template {$this->fileName}");
                echo $content;
                exit;
            }
            else
            {
                Trace::debug("Cache old for template {$this->fileName}, reinitialize");
            }
        }
        else
        {
            Trace::debug("Cache miss for template {$this->fileName}, reinitialize");
        }
    }

    public function setTemplate()
    {
        $content = ob_get_contents();
        Trace::Debug("Cache start for {$this->fileName}");
        $fh = fopen($this->cacheFilePath, 'w');
        fwrite($fh, $content);
        fclose($fh);
        ob_end_flush();
    }

    public function checkFileExist($file, $controllerPrefix)
    {
        $this->fileName = $file;
        $this->controllerPrefix = $controllerPrefix;
        $this->cacheFilePath = $this->cacheDir . md5($this->fileName) . '_' . $this->controllerPrefix . '_' . $this->fileName . $this->fileEXT;
        return file_exists($this->cacheFilePath);
    }
}