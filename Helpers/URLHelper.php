<?php


namespace Helper;


final class URLHelper
{

    /**
     * @param $url string URL to be validated
     * @return bool true if $url is an valid URL
     */
    public static function validateURL($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            return true;
        }
        return false;
    }

    /**
     * @param $url string URL to check it's host
     * @param $host string Host
     * @return bool true if $url host and $host is equal
     */
    public static function isHostEquals($url, $host)
    {
        if ((parse_url($url, PHP_URL_HOST) === $host) && (self::validateURL($url))) {
            return true;
        }
        return false;
    }

    /**
     * URLHelper private constructor.
     */
    private function __construct()
    {
    }
}