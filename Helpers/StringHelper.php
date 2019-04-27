<?php


namespace Helper;


final class StringHelper
{

    /**
     * @param $string string String to check
     * @param $startString string String to be checked if $string starts with
     * @return bool true if $string start with $startString
     */
    public static function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    /**
     * @param $input string String to be processed
     * @param $var_pos integer offset
     * @return bool|string String between quotes
     */
    public static function betweenQuotes($input, $var_pos) {
        $pos1 = strpos($input, "'", $var_pos);
        $pos2 = strpos($input, "'", $pos1 + 1);
        return substr($input, $pos1 + 1, $pos2 - $pos1 - 1);
    }

    /**
     * StringHelper private constructor.
     */
    private function __construct()
    {
    }
}