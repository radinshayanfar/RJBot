<?php


namespace Core;
use Exception;

include_once('Audio.php');


class SingleAudio extends Audio
{

    /**
     * SingleAudio constructor.
     * @param $url URLRedirect Redirect followed object
     * @param $hostFetchURL string Path to fetch host
     * @throws Exception If failed to fetch host
     */
    public function __construct($url, $hostFetchURL)
    {
        $this->hostFetchURL = $hostFetchURL;
        Media::__construct($url);
        Audio::__construct();
        $this->generateLinks();
    }


    /**
     * Fill $links array with audio link
     */
    protected function generateLinks()
    {
        $readableName = str_replace('-', ' ', $this->id);
        $this->links[$readableName] = $this->host . '/media/' . $this->currentMP3URL. '.mp3';
    }
}