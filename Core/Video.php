<?php

namespace Core;

use Exception;
use Helper\StringHelper;

include_once('Media.php');
include_once('../Helpers/StringHelper.php');

class Video extends Media
{

    private $qualitiesURL = array();

    /**
     * Video constructor.
     * @param $url URLRedirect Redirect followed object
     * @throws Exception If failed to fetch host
     */
    public function __construct($url)
    {
        $this->hostFetchURL = '/videos/video_host';
        Media::__construct($url);
        $this->processID();
        $this->processHost();
        $this->processQualities();
        $this->generateLinks();
    }

    /**
     * Process data and fill qualities array
     */
    private function processQualities()
    {
        $data = $this->url->getData();
        $permlink_pos = strpos($data, 'RJ.videoPermlink');
        $default_pos = strpos($data, "RJ.videoDefault", $permlink_pos);
        $data = substr($data, $permlink_pos + 16, $default_pos - 16 - $permlink_pos);
        $video_pos = -1;
        while ($video_pos = strpos($data, 'RJ.video', $video_pos + 1)) {
            $equal_pos = strpos($data, ' =', $video_pos);
            if (substr($data, $equal_pos, 8) == ' = null;') continue;
            $path = StringHelper::betweenQuotes($data, $video_pos);
            $quality = substr($data, $video_pos + 8, strpos($data, ' =', $video_pos) - $video_pos - 8);
            $this->qualitiesURL[$quality] = $path;
        }
    }

    protected function generateLinks()
    {
        foreach ($this->qualitiesURL as $quality => $link) {
            $this->links[$quality] = $this->host . $link;
        }
    }

    public function send($api, $message, $caption = '@RJ_DownloadBot')
    {
        $chat_id = $message['chat']['id'];
        $message_id = $message['message_id'];
        $text = '';
        foreach ($this->getLinks() as $name => $link) {
            $text .= $name . ': ' . $link . "\n";
        }
        $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text
        , 'disable_web_page_preview' => true);
        $api->postSend('sendMessage', $resp);
    }
}