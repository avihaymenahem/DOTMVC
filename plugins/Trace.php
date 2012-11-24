<?php

class Trace
{
    private static $logFile;

    private static function WriteToLogFile($prefix, $content)
    {
        self::$logFile = ROOT . DS . 'public' . DS . 'tmp' .DS . 'logs' . DS . 'trace.log';
        $handle = fopen(self::$logFile, 'a');
        $fullContent = "[" . date("Y-m-d H:i:s") . "] " . $prefix . $content . "\r\n";
        fwrite($handle, $fullContent);
        fclose($handle);
    }

    public static function debug($content)
    {
        self::WriteToLogFile('Debug: ',$content);
    }

    public function dump(array $content)
    {
        self::WriteToLogFile('Dump: ' ,print_r($content, true));
    }
}