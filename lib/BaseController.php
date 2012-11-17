<?php

abstract class BaseController
{
    private $controller;
    private $controllerExposed;
    private $action;
    private $queryString;
    private $displayView = true;
    private $displayLayout = true;
    private $layoutFile;
    private $view;
    private $otherViewFile;
    public $params;

    /**
     * @param $controller
     * @param $action
     * @param $queryString
     * @param bool $isInitedFromSpecialRoute
     */
    public function __construct($controller, $action, $queryString, $isInitedFromSpecialRoute = false)
    {
        $this->controller = $controller;
        $this->controllerExposed = lcfirst(str_replace("Controller", "", $controller));
        $this->queryString = $queryString;
        $this->action = $action;
        $this->layoutFile = DEFAULT_LAYOUT;

        $this->analyzeQueryStringIntoArray($isInitedFromSpecialRoute);
        $this->initView();
        $this->getControllerAct();
    }


    public function getControllerAct()
    {

        $action = $this->action . 'Action';
        if(!method_exists($this->controller, $action))
        {
            $action = $this->action = 'indexAction';
        }

        $this->onInit();
        $this->{$action}();
        $this->renderView();
    }

    private function initView()
    {
        $this->view = new stdClass();
        $this->view->title = DEFAULT_PAGE_TITLE;
    }

    /**
     * @param $viewFile
     */
    public function setView($viewFile)
    {
        if(file_exists(ROOT . DS . 'application' . DS . views . DS . $this->controllerExposed . DS . $viewFile))
        {
            $this->otherViewFile = $viewFile;
        }
    }

    /**
     * Render the view from the controller and action chosed
     * @return mixed
     * @throws Exception
     */
    public function renderView()
    {
        if($this->displayView)
        {
            $viewPath = ROOT . DS . 'application' . DS . 'views' . DS . $this->controllerExposed. DS;
            if(isset($this->otherViewFile))
            {
                $viewPath .= $this->otherViewFile;
            }
            else
            {
                $viewPath .= $this->action . '.phtml';
            }

            if(file_exists($viewPath))
            {
                $layoutPath = ROOT . DS . 'application' . DS . 'views' . DS . 'layout' . DS . $this->layoutFile;
                if($this->displayLayout)
                {
                    if(file_exists($layoutPath))
                    {
                        require_once($layoutPath);
                    }
                    else
                    {
                        throw new Exception("Layout File " . $this->layoutFile . " Has Not Found");
                    }
                }
                else
                {
                    require_once($viewPath);
                    return;
                }
            }
            else
            {
                throw new Exception("No View File Found In: " . $viewPath);
            }
        }
    }

    /**
     * Disable view for functions that procces information only
     */
    public function disableView()
    {
        $this->displayView = false;
    }

    /**
     * Set other layout then the default one
     * @param $layout - File Name
     */
    public function setLayout($layout)
    {
        $this->layoutFile = $layout;
    }

    /**
     * Disable layout on current view
     */
    public function disableLayout()
    {
        $this->displayLayout = false;
    }

    /**
     * Build Array From Query String
     * @param bool $isInitedFromSpecialRoute
     */
    private function analyzeQueryStringIntoArray($isInitedFromSpecialRoute = false)
    {
        $this->params = array();
        if(!$isInitedFromSpecialRoute) { array_shift($this->queryString); }
        array_shift($this->queryString);

        for($x=0; $x < count($this->queryString); $x++)
        {
            $this->params[$this->queryString[$x]] = $this->queryString[$x+1];
            array_shift($this->queryString);
        }
    }

    /**
     * Get query string as ordered array
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set View's Title
     * @param $str
     */
    public function setTitle($str)
    {
        $this->view->title = $str;
    }

    /**
     * Runs this function before calling to action on controller
     */
    public function onInit()
    {

    }
}