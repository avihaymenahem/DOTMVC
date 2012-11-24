<?php

class BaseView extends stdClass
{
    private static $instance;
    private $isLayoutEnabled;
    private $viewFilePath;
    private $viewFileName;
    private $layoutFilePath;
    private $layoutFileName;
    private $controllerName;
    private $requestParams;
    public $content;
    public $title;

    /**
     * @static
     * @return BaseView
     */
    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new BaseView();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->setLayout('default.phtml');
        $this->enableLayout();
    }

    public function render()
    {
        if($this->isViewExist())
        {
            if(CACHE_ENABLED)
            {
                $cache = OutputCache::getInstance($this->viewFileName, $this->controllerName);
                $htmlOutput = $cache->getTemplate();
                if($cache->fileIsOld() || !$htmlOutput)
                {
                    ob_start();
                    echo $this->getFile();
                    $htmlOutput = $cache->setTemplate();
                }
            }
            else
            {
                $htmlOutput = $this->getFile();
            }

            $htmlOutput = ENABLE_MINIFY ? Minify::html($htmlOutput) : $htmlOutput;
            echo $htmlOutput;
            exit;
        }
        else
        {
            throw new Exception("View File {$this->viewFileName} Dont Exist!");
        }
    }

    private function getFile($htmlOutput = false)
    {
        if(!$htmlOutput)
        {
            ob_start();
            require_once($this->viewFilePath);
            $htmlOutput = ob_get_contents();
            ob_end_clean();
        }

        if($this->isLayoutEnabled())
        {
            ob_start();
            $this->assign("content", $htmlOutput);
            require_once($this->layoutFilePath);
            $htmlOutput = ob_get_contents();
            ob_end_clean();
        }

        return $htmlOutput;
    }

    public function setController($controllerName) { $this->controllerName = $controllerName; }
    public function isLayoutEnabled() { return $this->isLayoutEnabled; }
    public function enableLayout() { $this->isLayoutEnabled = true; }
    public function disableLayout() { $this->isLayoutEnabled = false; }
    public function setLayout($file)
    {
        $this->layoutFileName = $file;
        $this->layoutFilePath = ROOT . DS . 'application' . DS . 'layout' . DS . $file;
    }

    public function isViewExist() { return file_exists($this->viewFilePath); }
    public function setView($tpl)
    {
        $this->viewFileName = $tpl;
        $this->viewFilePath = ROOT . DS . 'application' . DS . 'views' . DS . $this->controllerName . DS . $this->viewFileName;
    }

    public function setRequestParams($params) { $this->requestParams = $params; }
    public function getRequestParams() { return $this->requestParams; }

    public function setTitle($title) { $this->title = $title; }
    public function assign($key, $value) { $this->$key = $value; }
}