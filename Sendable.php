<?php


interface Sendable
{
    /**
     * @param $api TelegramAPI Telegram API object
     * @param $message array User sent message decoded to array
     */
    function send($api, $message);
}