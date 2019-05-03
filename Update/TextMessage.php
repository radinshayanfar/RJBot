<?php


namespace Update;

use Core\Album;
use Core\Audio;
use Core\SingleAudio;
use Core\URLRedirect;
use Core\Video;
use Database;
use Exception;
use Helper\Helper;
use Helper\StringHelper;
use Helper\URLHelper;
use MediaType;
use TelegramAPI;

class TextMessage
{
    private $message;
    private $api;
    private $db;

    /**
     * Message constructor.
     * @param $message array Array of message
     * @param $api TelegramAPI Telegram API object
     * @param $dbc Database Database connection object
     */
    public function __construct($message, $api, $dbc)
    {
        $this->message = $message;
        $this->api = $api;
        $this->db = $dbc;

        switch ($this->message['text']) {
            case '/start':
                $this->start();
                break;

            default:
                $this->downloader();
                break;
        }
    }

    /**
     * Answers /start command
     */
    private function start()
    {
        $chat_id = $this->message['chat']['id'];
        $user_FName = $this->message['chat']['first_name'];
        $text = "Hello {$user_FName}!\n";
        $text .= "I can help you download RadioJavan.com medias.\n";
        $text .= "Just send media's link from Radio Javan website or application to me!";
        $resp = array('chat_id' => $chat_id, 'text' => $text, 'disable_web_page_preview' => true);
        $this->api->webhookSend('sendMessage', $resp);
    }

    /**
     *
     * @param $url string
     * @return string
     * @throws Exception
     */
    private function determineType($url): string
    {
        if (!URLHelper::isHostEquals($url, 'www.radiojavan.com')) throw new Exception('Not a RadioJavan link.');
        $path = parse_url($url, PHP_URL_PATH);
        if (StringHelper::startsWith($path, '/mp3s/mp3/')) return MediaType::MUSIC;
        if (StringHelper::startsWith($path, '/videos/video/')) return MediaType::VIDEO;
        if (StringHelper::startsWith($path, '/podcasts/podcast/')) return MediaType::PODCAST;
        if (StringHelper::startsWith($path, '/mp3s/album/')) return MediaType::ALBUM;
        throw new Exception('Can\'t get media.');
    }

    private function downloader()
    {
        $originalText = $this->message['text'];
        $chat_id = $this->message['chat']['id'];
        $message_id = $this->message['message_id'];

        {
            $text = 'Sending media may take a few moment. Please wait...';
            $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text);
            $this->api->postSend('sendMessage', $resp);
            Helper::closeConnection();
        }

        try {
            $mediaType = '';
            $url = null;
            if (URLHelper::validateURL($originalText)) {
                $url = new URLRedirect($originalText);
                $mediaType = $this->determineType($url->getRedirectedURL());
            }
            else
                throw new Exception('Can\'t get media.');
//            $caption = '@RJ_DownloadBot';
            $media = null;
            if ($mediaType == MediaType::MUSIC) {
                $media = new SingleAudio($url, Audio::MP3_HOST);
            }
            if ($mediaType == MediaType::VIDEO) {
                $media = new Video($url);
            }
            if ($mediaType == MediaType::PODCAST) {
                $media = new SingleAudio($url, Audio::PODCAST_HOST);
            }
            if ($mediaType == MediaType::ALBUM) {
//            file_put_contents('dump2.txt', $dbc->autoIncrementStart());
                $media = new Album($url, $this->db->autoIncrementStart());
                $this->db->addTracksLink($media->getLinks());
            }
            $media->send($this->api, $this->message);
        } catch (Exception $e) {
            $text = $e->getMessage();
            $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text);
            $this->api->postSend('sendMessage', $resp);
        }


    }
}