<?php

class Bootstrap
{
    private static $instance;
    private $configArray;

    /**
     * @static
     * @return Bootstrap
     */
    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance = new Bootstrap();

        return self::$instance;
    }

    private function __construct()
    {
        $this->setBasicVars();
        $this->getConfig();
        $this->setAutoloader();
        $this->setExceptionHandler();
        Shared::setReporting();
        Shared::removeMagicQuotes();
        Shared::unregisterGlobals();
        $router = Router::getInstance();
        $this->initRoutes();
        $router->callHook();
    }

    private function setBasicVars()
    {
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT', dirname(dirname(__FILE__)));
    }

    private function initRoutes() { require_once(ROOT . DS . 'config' . DS . 'routes.php'); }
    /**
     * @throws Exception
     */
    private function getConfig()
    {
        if(count($this->configArray))
        {
            foreach($this->configArray as $configName)
            {
                $configFile = dirname(dirname(__FILE__)) . '/config/' . $configName . '.ini';
                if(file_exists($configFile))
                {
                    $config = parse_ini_file($configFile);

                    foreach($config as $key => $value)
                    {
                        define($key, $value);
                        unset($config[$key]);
                    }

                    unset($config);
                }
                else
                {
                    throw new Exception("Config File Not Found");
                }
            }
        }
        else
        {
            $this->addConfig('default');
            $this->getConfig();
        }
    }

    /**
     * @param $fileName
     */
    public function addConfig($fileName)
    {
        $this->configArray[] = $fileName;
    }

    private function setExceptionHandler()
    {
        set_exception_handler(array(EXCEPTION_HANDLER_CLASS, EXCEPTION_HANDLER_METHOD));
    }

    private function setAutoloader()
    {
        function __autoload($className)
        {
            $className = ucfirst($className);
            $fileIsLib          = ROOT . DS . 'lib' . DS . $className . '.php';
            $fileInPlugins      = ROOT . DS . 'plugins' . DS . $className . '.php';
            $fileIsModel        = ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php';
            $fileIsController   = ROOT . DS . 'application' . DS . 'controllers' . DS . $className . '.php';

            if(file_exists($fileIsLib))
            {
                require_once($fileIsLib);
            }
            elseif(file_exists($fileIsController))
            {
                require_once($fileIsController);
            }
            elseif(file_exists($fileIsModel))
            {
                require_once($fileIsModel);
            }
            elseif(file_exists($fileInPlugins))
            {
                require_once($fileInPlugins);
            }
        }
    }
}