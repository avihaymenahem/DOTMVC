<?php

class ExceptionController
{
    /**
     * @param $e
     */
    public function handleExceptions($e)
    {
        if(defined('DEVELOPMENT_ENVOIRMENT') && DEVELOPMENT_ENVOIRMENT == true)
        {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
        }
        else
        {
            require_once(ROOT . DS . 'application' . DS . 'views' . DS . 'common' . DS . '404.phtml');
        }

        ExceptionController::writeToErrorLog($e);
    }

    /**
     * @static
     * @param $e
     */
    private static function writeToErrorLog($e)
    {
        $errorLogFilePath = ROOT . DS . 'tmp' . DS . 'logs' . DS . 'error.log';
        $handle = fopen($errorLogFilePath, 'a');
        $message = $e->getMessage();
        $stringToWrite = "[" . time() . "] Error: $message \r\n";
        fwrite($handle, $stringToWrite);
        fclose($handle);
    }
}