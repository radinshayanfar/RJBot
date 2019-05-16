<?php


class SendableException extends Exception implements Sendable
{
    /**
     * SendableException constructor.
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }


    /**
     * @param $api TelegramAPI Telegram API object
     * @param $message array User sent message decoded to array
     */
    function send($api, $message)
    {
        $chat_id = $message['chat']['id'];
        $message_id = $message['message_id'];
        $text = $this->getMessage();
        $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text);
        $api->postSend('sendMessage', $resp);
    }
}