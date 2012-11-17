<?php

/**
 * Abstract class that behave's as a basic db controller
 */
abstract class AbstractDAO
{
    /* Singleton initializer */
    protected  static $instance;

    /* Db Type Holder */
    private $db;
    /* Flag to determin if db is connected*/
    private $connection;
    /* The XML file that used as the current queries mapper */
    private $xmlFile;
    /* Arguments sent from the query call, An instance ob the appropriate model */
    private $arguments;
    /* Last query called */
    private $lastQuery;
    /* Last query called name */
    private $lastQueryName;
    /* Last Inserted ID on insert query */
    private $lastInsertID;
    /* Objects Count of the last query */
    private $lastQueryResultCount;


    abstract private function __construct();
    abstract public static function getInstance();

    /**
     * @param $db
     */
    public function connect($db)
    {
        $this->db = ucfirst($db).'DAO';

        switch($this->db)
        {
            case 'MasterDAO':
            default:
                $this->connection = new PDO("mysql:host=" . MASTER_DBHOST . ";dbname=" . MASTER_DBNAME , MASTER_DBUSER, MASTER_DBPASS);
                break;
        }
    }

    /**
     * @param bool $isOneRow
     * @param bool $isReturn
     * @return null
     */
    private function getQueryExec($isOneRow = false, $isReturn = true)
    {
        $this->xmlFile = ROOT . DS . 'application' . DS . 'models' . DS . 'sqlMaps' . DS . $this->db.'.xml';
        $xml = simplexml_load_file($this->xmlFile);
        $queryID = array_shift($this->arguments);
        $result = $xml->xpath('/'.$this->db.'/query[@id="'.$queryID.'"]');
        $result = (array) $result[0];
        $resultMapNoPrefix = ucfirst($result['@attributes']['resultMap']);
        $resultMap = $resultMapNoPrefix . 'Map';
        $this->lastQuery = $result[0];
        $this->lastQueryName = $queryID;

        if($this->arguments)
        {
            $getObjectFromArgs = $this->arguments[0];
            $declaredFunctions = get_class_methods($getObjectFromArgs);
            if($declaredFunctions)
            {
                foreach($declaredFunctions as $singleFunction)
                {
                    $getOrSetFunction = substr($singleFunction, 0, 3);
                    if($getOrSetFunction == 'get')
                    {
                        $data = $getObjectFromArgs->$singleFunction();
                        if(!empty($data))
                        {
                            $functionVarName = lcfirst(substr($singleFunction, 3));
                            $this->lastQuery = preg_replace('/\#'.$functionVarName.'\#/', $data, $this->lastQuery, 1);
                        }
                    }
                }
            }
        }

        return $this->execute($isOneRow, $isReturn, $resultMap);
    }

    /**
     * @param $isOneRow
     * @param $isReturn
     * @param $resultMap
     * @return null
     */
    private function execute($isOneRow, $isReturn, $resultMap)
    {
        $queryInit = $this->connection->query($this->lastQuery);
        if($queryInit)
        {
            if($isReturn)
            {
                $queryInit->setFetchMode( PDO::FETCH_CLASS, $resultMap);
                $result = $isOneRow? $queryInit->fetch() : $queryInit->fetchAll();
                $this->lastQueryResultCount = count($result);
                return $result;
            }
            else
            {
                return $this->lastInsertID = $this->connection->lastInsertId();
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * @param bool $oneRow
     * @return null
     */
    private function returnAbleFunction($oneRow = false)
    {
        return $this->getQueryExec($oneRow, true);
    }

    /**
     * @return null
     */
    private function unReturnAbleFunction()
    {
        return $this->getQueryExec(false, false);
    }

    /**
     * @return null
     */
    public function selectOne()
    {
        $this->arguments = func_get_args();
        return $this->returnAbleFunction(true);
    }

    /**
     * @return null
     */
    public function select()
    {
        $this->arguments = func_get_args();
        return $this->getQueryExec();
    }

    /**
     * @return null
     */
    public function delete()
    {
        $this->arguments = func_get_args();
        return $this->unReturnAbleFunction();
    }

    /**
     * @return null
     */
    public function update()
    {
        $this->arguments = func_get_args();
        return $this->unReturnAbleFunction();
    }

    /**
     * @return null
     */
    public function insert()
    {
        $this->arguments = func_get_args();
        return $this->unReturnAbleFunction();
    }
}