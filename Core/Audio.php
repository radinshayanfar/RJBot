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

    protected $currentMP3URL;

    /**
     * Audio constructor.
     */
    protected function __construct()
    {
        $this->currentMP3URL = $this->processRJCurrentMP3URL();
    }

    /**
     * Processes data and returns currentMP3URL field
     * @return bool|string currentMP3URL
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

        $currentMP3URL = json_decode($result, true)['currentMP3Url'];
        if ($currentMP3URL === null) {
            throw new Exception($GLOBALS["_STR"]["ERRORS"]["get_mp3_url_error"]);
        }
        return $currentMP3URL;
    }
}