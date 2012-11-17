<?php

class Router
{
    private static $instance;

    private $controller;
    private $action;
    private $queryString;
    private $displayView;
    private $displayLayout;
    private $specialRoutes;
    private $isInitFromSpecialRoutes;

    /**
     * @static
     * @return Router
     */
    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->displayView = true;
        $this->displayLayout = false;
        $this->isInitFromSpecialRoutes = false;
    }

    public function callHook()
    {
        $queryString = explode('/', $_SERVER["REQUEST_URI"]);
        array_shift($queryString);

        if(count($queryString))
        {
            $controllerName = $queryString[0];

            if(isset($this->specialRoutes[$controllerName]))
            {
                $currentSpecialRoute = $this->specialRoutes[$controllerName];
                $action = $currentSpecialRoute['action'];
                $controllerName = $currentSpecialRoute['controller'];
                $this->isInitFromSpecialRoutes = true;
            }
            else
            {
                $action = isset($queryString[1]) ? $queryString[1] : 'index';
            }

            $this->action = $action;
            $this->controller = ucfirst($controllerName) . 'Controller';
            $this->queryString = $queryString;
        }

        $this->dispatch();
    }

    /**
     * @throws Exception
     */
    public function dispatch()
    {
        if(!isset($this->controller) || $this->controller === 'Controller')
        {
            $this->controller = 'IndexController';
            $this->dispatch();
        }
        elseif(class_exists($this->controller))
        {
            $actionFullName = $this->action . 'Action';
            $dispatch = new $this->controller($this->controller ,$this->action, $this->queryString, $this->isInitFromSpecialRoutes);

            if(!isset($this->action) || $actionFullName == 'Action')
            {
                $this->action = 'index';
                $this->dispatch();
            }
            elseif(method_exists($this->controller, $actionFullName))
            {
                $actionArray = array($dispatch, $actionFullName);
                if(isset($this->queryString))
                {
                    call_user_func_array($actionArray, $this->queryString);
                }
                else
                {
                    call_user_func($actionArray);
                }
            }
            else
            {
                throw new Exception($actionFullName . " Controller Action Not Found");
            }
        }
        else
        {
            throw new Exception($this->controller ? $this->controller . ' Empty Controller' : $this->controller . " Not Found");
        }
    }

    /**
     * @param array $route
     */
    public function addRoute(array $route)
    {
        $routeName = $route['name'];
        $this->specialRoutes[$routeName] = $route;
    }
}