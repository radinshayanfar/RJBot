<?php


namespace Update;


use Core\SingleAudio;
use Core\URLRedirect;
use Database;
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
        $resp = array('callback_query_id' => $callback_query_id, 'text' => 'Sending. Please wait...');
        $this->api->postSend('answerCallbackQuery', $resp);
    }

    private function processRequest()
    {
        $data= $this->callback_query['data'];
        $url = new URLRedirect($this->db->getTrackLinkByID($data));
        $media = new SingleAudio($url, SingleAudio::MP3_HOST);
        $media->send($this->api, $this->callback_query['message']['reply_to_message']);
    }


}