<?php

namespace Update;

include_once('MediaType.php');
include_once('TextMessage.php');
include_once('CallbackQuery.php');

use Database;
use TelegramAPI;

class Action
{
    private $update;
    private $api;
    private $db;

    /**
     * Action constructor.
     * @param $update array Given update decoded from json to array
     * @param $api TelegramAPI Telegram API class
     * @param $dbc Database Database connection
     */
    public function __construct($update, $api, $dbc)
    {
        $this->update = $update;
        $this->api = $api;
        $this->db = $dbc;

        if (isset($update['message'])) {
            $this->messageProcess();
        }
        if (isset($update['callback_query'])) {
            $this->callbackQueryProcess();
        }
    }

    private function messageProcess() {
        if (!isset($this->update['message']['text'])) {
            $chat_id = $this->update['message']['chat']['id'];
            $resp = array('chat_id' => $chat_id, 'text' => 'Please send a Radio Javan media link.');
            $this->api->webhookSend('sendMessage', $resp);
            return;
        }

        new TextMessage($this->update['message'], $this->api, $this->db);
    }

    private function callbackQueryProcess() {
        new CallbackQuery($this->update['callback_query'], $this->api, $this->db);
//        $chat_id = $this->update['callback_query']['message']['chat']['id'];
//        $data= $this->update['callback_query']['data'];
//        $resp = array('chat_id' => $chat_id, 'text' => $data);
//        $this->api->postSend('sendMessage', $resp);
    }


}