<?php


namespace Update;


use Core\SingleAudio;
use Core\URLRedirect;
use Database;
use Exception;
use TelegramAPI;

class CallbackQuery
{
    private $callback_query;
    private $api;
    private $db;

    /**
     * CallbackQuery constructor.
     * @param $callback_query array CallBackQuery object
     * @param $api TelegramAPI Telegram API Object
     * @param $db Database Database connection object
     */
    public function __construct($callback_query, $api, $db)
    {
        $this->callback_query = $callback_query;
        $this->api = $api;
        $this->db = $db;

        $this->answerCallbackQuery();
        $this->processRequest();
    }

    private function answerCallbackQuery() {
        $callback_query_id = $this->callback_query['id'];
        $resp = array('callback_query_id' => $callback_query_id, 'text' => 'Sending. Please wait...'
                    , 'cache_time' => 3);
        $this->api->postSend('answerCallbackQuery', $resp);
    }

    private function processRequest()
    {
        $data= $this->callback_query['data'];
        $url = new URLRedirect($this->db->getTrackLinkByID($data));
        try {
            $media = new SingleAudio($url, SingleAudio::MP3_HOST);
            $media->send($this->api, $this->callback_query['message']['reply_to_message']);
        } catch (Exception $e) {
            $text = $e->getMessage();
            $resp = array('chat_id' => $this->callback_query['message']['chat']['id'],
                'reply_to_message_id' => $this->callback_query['message']['reply_to_message']['message_id'], 'text' => $text);
            $this->api->postSend('sendMessage', $resp);
        }
    }


}