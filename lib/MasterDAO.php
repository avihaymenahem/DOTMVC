<?php

class MasterDAO extends AbstractDAO
{
    /**
     * @static
     * @return MasterDAO
     */
    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance = new MasterDAO();
        return self::$instance;
    }

    private function __construct()
    {
        $this->connect(MASTER_NAME);
    }

    /**
     * Demo Function
     * @param $errorObject
     * @return null
     */
    public function getErrorByErrorCode($errorObject)
    {
        return $this->selectOne('getErrorByErrorCode', $errorObject);
    }
}