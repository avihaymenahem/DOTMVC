<?php

class Minify
{

    public static function html($content)
    {
        $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
        $replace = array('>','<','\\1');
        $buffer = preg_replace($search, $replace, $content);
        return $buffer;
    }

    public static function css($files)
    {
        return Minify::compress('css', $files);
    }

    public static function js($files)
    {
        return Minify::compress('js', $files);
    }

    public static function compress($type, $files)
    {
        $tmpContent = "";
        $libraryPath = ROOT . DS . 'public' . DS . 'static' . DS . $type . DS;
        $newFilePath = ROOT . DS . 'public' . DS . 'tmp' . DS . 'outputCache' . DS;
        $staticResources = is_array($files) ? $files : array($files);
        $fileNames = implode("_", $staticResources);
        foreach($staticResources as $file)
        {
            $thisFilePath = $libraryPath . $file . '.' . $type;
            ob_start();
            echo file_get_contents($thisFilePath);
            $tmpContent .= ob_get_contents();
            ob_end_clean();
        }

        $tmpContent = trim($tmpContent);
        switch($type)
        {
            case 'css':
                $tmpContent = preg_replace('!/\*.*?\*/!s','', $tmpContent);
                $tmpContent = preg_replace('/\n\s*\n/',"\n", $tmpContent);
                $tmpContent = preg_replace('/[\n\r \t]/',' ', $tmpContent);
                $tmpContent = preg_replace('/ +/',' ', $tmpContent);
                $tmpContent = preg_replace('/ ?([,:;{}]) ?/','$1',$tmpContent);
                $tmpContent = preg_replace('/;}/','}',$tmpContent);
                break;

            case 'js':
                $tmpContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $tmpContent);
                $tmpContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $tmpContent);
                break;
        }

        $newFileName = time() . '_' . $fileNames . '.min.' . $type;
        $newFilePath .= $newFileName;
        $filePathForReturn = BASE_URL . 'public' . DS . 'tmp' . DS . 'outputCache' . DS . $newFileName;
        $fh = fopen($newFilePath, 'w');
        fwrite($fh, $tmpContent);
        fclose($fh);
        return $filePathForReturn;
    }
}