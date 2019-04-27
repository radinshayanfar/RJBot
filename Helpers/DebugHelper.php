<?php


namespace Helper;


class DebugHelper
{
    /**
     * @param $var mixed variable which dumps to file
     * @param $file string Relative or absolute path to file
     */
    static function dump_to_file($var, $file)
    {
        ob_start();
        var_dump($var);
        file_put_contents($file, ob_get_clean());
    }

    /**
     * @param $text string text which appends to end of file
     * @param $fileName string Relative or absolute path to file
     */
    static function append_to_file($text, $fileName)
    {
        $MyFile = fopen($fileName, "a");
        fwrite($MyFile, "\n" . $text);
        fclose($MyFile);
    }

    /**
     * DebugHelper private constructor.
     */
    private function __construct()
    {
    }
}