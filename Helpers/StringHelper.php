<?php


namespace Helper;


use DOMDocument;

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
    public static function betweenQuotes($input, $var_pos)
    {
        $pos1 = strpos($input, "'", $var_pos);
        $pos2 = strpos($input, "'", $pos1 + 1);
        return substr($input, $pos1 + 1, $pos2 - $pos1 - 1);
    }

    public static function extractSetCookies($input)
    {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $input, $matches);
        $cookies = array();
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        return $cookies;
    }

    public static function getCSRFFromMeta($input)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($input);

        $metas = $doc->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('name') == 'csrf-token')
                return $meta->getAttribute('content');
        }
    }

    /**
     * StringHelper private constructor.
     */
    private function __construct()
    {
    }
}