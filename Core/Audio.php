<?php


namespace Core;

use Exception;
use Helper\DebugHelper;
use Helper\StringHelper;

include_once('Media.php');


abstract class Audio extends Media
{
    const MP3_HOST = '/mp3s/mp3_host';
    const PODCAST_HOST = '/podcasts/podcast_host';

    protected $setupJson;

    /**
     * Audio constructor.
     */
    protected function __construct()
    {
        $this->setupJson = $this->processRJCurrentMP3URL();
    }

    // TODO: name maybe should be changed
    /**
     * Sends ?setup=1 request to get track(s) info
     * @return bool|array currentMP3URL
     */
    private function processRJCurrentMP3URL()
    {
        $csrf = StringHelper::getCSRFFromMeta($this->url->getData());
        $headers = [
            'X-CSRF-Token: ' . $csrf,
            'X-Requested-With: XMLHttpRequest',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url->getRedirectedURL() . '?setup=1');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) throw new Exception($GLOBALS["_STR"]["ERRORS"]["timeout"]);
        if (curl_errno($ch)) throw new Exception($GLOBALS["_STR"]["ERRORS"]["unknown"]);
//        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) throw new Exception('Unknown error occurred. Please try again in a moment');
        curl_close($ch);

        $setupJson = json_decode($result, true);
        if ($setupJson === null) {
            throw new Exception($GLOBALS["_STR"]["ERRORS"]["get_setup_error"]);
        }
        return $setupJson;
    }
}