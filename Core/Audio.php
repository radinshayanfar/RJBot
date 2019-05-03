<?php


namespace Core;

use Helper\StringHelper;

include_once('Media.php');
include_once('../Helpers/StringHelper.php');


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
        $var_pos = strpos($this->url->getData(), 'RJ.currentMP3Url');
        return StringHelper::betweenQuotes($this->url->getData(), $var_pos);
    }
}