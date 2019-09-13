<?php


namespace Update;

use Core\Album;
use Core\Audio;
use Core\SingleAudio;
use Core\URLRedirect;
use Core\Video;
use Database;
use Exception;
use Helper\DebugHelper;
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

            case '/help':
                $this->help();
                break;

            default:
                $this->extract();
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
        $text .= "I can help you download media from RadioJavan.com.\n";
        $text .= "Get /help";
        $resp = array('chat_id' => $chat_id, 'text' => $text, 'disable_web_page_preview' => true);
        $this->api->webhookSend('sendMessage', $resp);
    }

    /**
     * Answers /help command
     */
    private function help()
    {
        $chat_id = $this->message['chat']['id'];
        $text = "There are two ways to use me:\n";
        $text .= "1. Sending media link from RadioJavan website or application.\n";
        $text .= "2. Typing media or artist name to me and I'll search RadioJavan for results.\n\n";
        $text .= "Keep note that currently supported media are:\n";
        $text .= "Musics, Albums, Podcasts and Videos";
        $resp = array('chat_id' => $chat_id, 'text' => $text, 'disable_web_page_preview' => true);
        $this->api->webhookSend('sendMessage', $resp);
    }

    /**
     * Determines media type based on url
     * @param $url string Redirected url
     * @return string one of MediaType constants
     * @throws Exception if the host is not RadioJavan's host
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

    /**
     * Extracts and send media
     */
    private function extract()
    {
        $originalText = $this->message['text'];
        $chat_id = $this->message['chat']['id'];
        $message_id = $this->message['message_id'];

//        {
//            $text = 'Sending media may take a few moment. Please wait...';
//            $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text);
//            $this->api->postSend('sendMessage', $resp);
//            Helper::closeConnection();
//        }

        try {
            $mediaType = '';
            $url = null;
            if (URLHelper::validateURL($originalText)) {
                $url = new URLRedirect($originalText);
                $mediaType = $this->determineType($url->getRedirectedURL());
            } else {
                $this->search();
                return;
//                throw new Exception('Can\'t get media.');
            }
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

    /**
     * @throws Exception
     */
    private function search()
    {
        $query = $this->message['text'];
        $chat_id = $this->message['chat']['id'];
        $message_id = $this->message['message_id'];
        $NO_RESULT_TEXT = "No Search Results\n\nPlease note:
1- Keywords must be exactly typed as RadioJavan website.
2- I can only search name of media, not lyrics.
3- Keywords are in English characters.";

        $url = new URLRedirect('https://www.radiojavan.com/');
        $rj_web_cookie = StringHelper::extractSetCookies($url->getData())['_rj_web'];
        $csrf = StringHelper::getCSRFFromMeta($url->getData());

        $headers = [
            'Cookie: _rj_web=' . $rj_web_cookie,
            'X-CSRF-Token: ' . $csrf,
            'X-Requested-With: XMLHttpRequest',
        ];

        $url = new URLRedirect('https://www.radiojavan.com/search?q=' . urlencode($query), $headers);
        $query_data = $url->getData();

        if ($json_start_pos = strpos($query_data, '{')) {
            $json_data = '[' . str_replace("}\n{", '},{', substr($query_data, $json_start_pos)) . ']';
            $query = json_decode($json_data);

            $links = array();
            foreach ($query as $media) {
                switch ($media->category) {
                    case 'MP3':
                        $links['Music: ' . $media->artist . ' - ' . $media->song] = 'https://www.radiojavan.com' . $media->link;
                        break;
                    case 'Podcast':
                        $links['Podcast: ' . $media->title] = 'https://www.radiojavan.com' . $media->link;
                        break;
                    case 'Video':
                        $links['Video: ' . $media->artist . ' - ' . $media->song] = 'https://www.radiojavan.com' . $media->link;
                        break;
                    case 'Album':
                        $links['Album: ' . $media->artist . ' - ' . $media->album] = 'https://www.radiojavan.com' . $media->link;
                        break;
                }
                if (count($links) >= 10)
                    break;
            }
            if (count($links) == 0) {
                throw new Exception($NO_RESULT_TEXT);
            }

            $auto_increment_start = $this->db->autoIncrementStart();
            $this->db->addTracksLink($links);

            $text = 'Search results:';
            $inline_keyboard_key = array();
            foreach ($links as $name => $link) {
                $inline_keyboard_key[] = [['text' => $name, 'callback_data' => $auto_increment_start++]];
            }
            $resp = array('chat_id' => $chat_id, 'text' => $text, 'reply_to_message_id' => $message_id,
                'reply_markup' => array('inline_keyboard' => $inline_keyboard_key));
            $this->api->postSend('sendMessage', $resp);
        } else {
            throw new Exception($NO_RESULT_TEXT);
        }
    }
}