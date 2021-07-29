<?php

namespace Update;

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

    /**
     * This method will be invoked whenever sent message is a message not a callback query or etc.
     * Checks if it's text message and creates new TextMessage object to process it
     */
    private function messageProcess()
    {
        if (!isset($this->update['message']['text'])) {
            $chat_id = $this->update['message']['chat']['id'];
            $message_id = $this->update['message']['message_id'];
            $resp = array('chat_id' => $chat_id, 'text' => $GLOBALS["_STR"]["not_text"], 'reply_to_message_id' => $message_id);
            $this->api->webhookSend('sendMessage', $resp);
            return;
        }

        new TextMessage($this->update['message'], $this->api, $this->db);
    }

    /**
     * This method will be invoked whenever sent message is a callback query not a message or etc.
     * Creates new CallbackQuery object to process it
     */
    private function callbackQueryProcess()
    {
        new CallbackQuery($this->update['callback_query'], $this->api, $this->db);
    }


}