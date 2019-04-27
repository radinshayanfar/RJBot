<?php


namespace Core;
use Exception;

include_once('Audio.php');


class Album extends Audio
{

    private $tracksNames = array();

    /**
     * Album constructor.
     * @param $url URLRedirect Redirect followed object
     * @throws Exception If failed to fetch host
     */
    public function __construct($url)
    {
        $this->hostFetchURL = Audio::MP3_HOST;
        Media::__construct($url);
        Audio::__construct();
//        $this->processID();
//        $this->processHost();
        $this->processTracks();
        $this->generateLinks();
    }

    protected function processID()
    {
        $id = substr($this->currentMP3URL, strrpos($this->currentMP3URL, '/') + 1);
        $this->id = $id;
    }

    protected function generateLinks()
    {
//        $path = substr($this->currentMP3URL, 0, strrpos($this->currentMP3URL, '/'));
        foreach ($this->tracksNames as $track)
        {
            $this->links[$track] = 'https://www.radiojavan.com/mp3s/mp3/' . $track;
        }
    }

    private function processTracks()
    {
        $data = $this->url->getData();
        $var_pos = strpos($data, 'RJ.relatedMP3');
        $opening_bracket = strpos($data, '[', $var_pos);
        $semicolon_pos = strpos($data, ';', $var_pos);
        $tracksArray = json_decode(substr($data, $opening_bracket, $semicolon_pos - $opening_bracket), true);
        foreach ($tracksArray as $track) {
            $this->tracksNames[] = $track['mp3'];
        }
    }

    public function send($bot, $update, $caption)
    {
        $chat_id = $update['message']['chat']['id'];
        $message_id = $update['message']['message_id'];
        $text = 'Select track:';
        $inline_keyboard_key = array();
        foreach ($this->getLinks() as $name => $link) {
            $inline_keyboard_key[] = [['text' => str_replace('-', ' ', $name), 'callback_data' => '1']];
        }
        $resp = array ('chat_id' => $chat_id, 'text' => $text, 'reply_to_message_id' => $message_id,
            'reply_markup' => array('inline_keyboard' => $inline_keyboard_key));
        $bot->postSend('sendMessage', $resp);
    }
}