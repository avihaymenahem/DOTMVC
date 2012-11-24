<?php

abstract class BaseController
{
    private $controller;
    private $controllerExposed;
    private $action;
    private $queryString;
    private $layoutFile;
    /** @var BaseView */
    public $view;
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
        $this->getControllerAct();
    }


    private function getControllerAct()
    {
        $action = $this->action . 'Action';
        if(!method_exists($this->controller, $action))
        {
            $action = $this->action = 'indexAction';
        }

        $this->initView();
        $this->onInit();
        $this->{$action}();
    }

    /**
     * Render the view from the controller and action chosed
     */
    public function initView()
    {
        $this->view = BaseView::getInstance();
        $this->view->setTitle(DEFAULT_PAGE_TITLE);
        $this->view->setController($this->controllerExposed);
        $this->view->setView($this->action . '.phtml');
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
     * Runs this function before calling to action on controller
     */
    public function onInit() {}

    public function assign($key, $value)
    {
        $this->view->{$key} = $value;
    }
}