<?php


namespace Helper;


final class Helper
{

    public static function closeConnection()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        ob_start();
        // do initial processing here
        // echo $response; // send the response
        header('Connection: close');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
        ob_flush();
        flush();
    }

    /**
     * Helper private constructor.
     */
    private function __construct()
    {
    }
}