<?php


namespace Update;


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

    /**
     * Answers callback query
     */
    private function answerCallbackQuery() {
        $callback_query_id = $this->callback_query['id'];
        $resp = array('callback_query_id' => $callback_query_id, 'text' => $GLOBALS["_STR"]["sending"]
                    , 'cache_time' => 3);
        $this->api->postSend('answerCallbackQuery', $resp);
    }

    /**
     * Get link from database by its id and creates new Media object to process it
     */
    private function processRequest()
    {
        $data= $this->callback_query['data'];
        try {
            $this->callback_query['message']['text'] = $this->db->getTrackLinkByID($data);
            new TextMessage($this->callback_query['message'], $this->api, $this->db);
        } catch (Exception $e) {
            $text = $e->getMessage();
            $resp = array('chat_id' => $this->callback_query['message']['chat']['id'],
                'reply_to_message_id' => $this->callback_query['message']['reply_to_message']['message_id'], 'text' => $text);
            $this->api->postSend('sendMessage', $resp);
        }
    }


}