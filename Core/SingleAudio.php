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
        $this->processID();
        $this->processHost();
        $this->generateLinks();
    }


    /**
     * Fill $links array with audio link
     */
    protected function generateLinks()
    {
        $readableName = str_replace('-', ' ', $this->id);
        $this->links[$readableName] = $this->host . '/media/' . $this->currentMP3URL . '.mp3';
    }


    public function send($api, $message, $caption = '@RJ_DownloadBot')
    {
        $document = reset($this->getLinks());
        $chat_id = $message['chat']['id'];
        $message_id = $message['message_id'];
        if ($this->hostFetchURL == self::MP3_HOST) {
            $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'document' => $document,
                'caption' => $caption);
            $api->postSend('sendDocument', $resp);
        }
        $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $document
        , 'disable_web_page_preview' => true);
        $api->postSend('sendMessage', $resp);
    }
}