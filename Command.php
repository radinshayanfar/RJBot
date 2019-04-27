<?php

namespace Command;

use Core\Album;
use Core\Audio;
use Core\SingleAudio;
use Core\URLRedirect;
use Core\Video;
use Exception;
use Helper\StringHelper;
use Helper\URLHelper;
use MediaType;

include_once('MediaType.php');

function close_connection()
{
    ignore_user_abort(true);
    set_time_limit(0);

    ob_start();
    // do initial processing here
    // echo $response; // send the response
    header('Connection: close');
    header('Content-Length: ' . ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();
}

function start($update)
{
    global $bot;
    $chat_id = $update['message']['chat']['id'];
    $user_FName = $update['message']['chat']['first_name'];
    $text = "Hello {$user_FName}!\n";
    $text .= "I can help you download RadioJavan.com medias.\n";
    $text .= "Just send media's link from Radio Javan website or application to me!";
    $resp = array('chat_id' => $chat_id, 'text' => $text, 'disable_web_page_preview' => true);
    $bot->webhookSend('sendMessage', $resp);
}

function determineType($rawURL): string
{
    $url = new URLRedirect($rawURL);

    if (!URLHelper::isHostEquals($url, 'www.radiojavan.com')) throw new Exception('Not a RadioJavan link.');
    $path = parse_url($url->getRedirectedURL(), PHP_URL_PATH);
    if (StringHelper::startsWith($path, '/mp3s/mp3/')) return MediaType::MUSIC;
    if (StringHelper::startsWith($path, '/videos/video/')) return MediaType::VIDEO;
    if (StringHelper::startsWith($path, '/podcasts/podcast/')) return MediaType::PODCAST;
    if (StringHelper::startsWith($path, '/mp3s/album/')) return MediaType::ALBUM;
    throw new Exception('Can\'t get media.');
}

function downloader($update)
{
    global $bot;

    $originalText = $update['message']['text'];
    $chat_id = $update['message']['chat']['id'];
    $message_id = $update['message']['message_id'];

    {
        $text = 'Sending media may take a few moment. Please wait...';
        $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text);
        $bot->postSend('sendMessage', $resp);
        close_connection();
    }

    try {
//        list($document, $id) = get_download_url($url);
        if (URLHelper::validateURL($originalText))
            $mediaType = determineType($originalText);
        else {
            throw new Exception('Can\'t get media.');
        }
        $caption = '@RJ_DownloadBot';
        $media = null;
        $resp1 = null;
        $resp2 = null;
        switch ($mediaType) {
            case MediaType::MUSIC:
                $media = new SingleAudio($originalText, Audio::MP3_HOST);
                $document = reset($media->getLinks());
                $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'document' => $document,
                    'caption' => $caption);
                $bot->postSend('sendDocument', $resp);
                $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $document
                , 'disable_web_page_preview' => true);
                $bot->postSend('sendMessage', $resp);
                break;
            case MediaType::VIDEO:
                $media = new Video($originalText);
                $message = '';
                foreach ($media->getLinks() as $name => $link) {
                    $message .= $name . ': ' . $link . "\n";
                }
                $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $message
                , 'disable_web_page_preview' => true);
                $bot->postSend('sendMessage', $resp);
                break;
            case MediaType::PODCAST:
                $media = new SingleAudio($originalText, Audio::PODCAST_HOST);
                $document = reset($media->getLinks());
                $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'document' => $document,
                    'caption' => $caption);
                $bot->postSend('sendDocument', $resp);
                $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $document
                , 'disable_web_page_preview' => true);
                $bot->postSend('sendMessage', $resp);
                break;
            case MediaType::ALBUM:
                $media = new Album($originalText);
                foreach ($media->getLinks() as $name => $link) {
                    $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'document' => $link,
                        'caption' => $caption);
                    $bot->postSend('sendDocument', $resp);
                }
                $message = '';
                foreach ($media->getLinks() as $name => $link) {
                    $message .= $name . ': ' . $link . "\n";
                }
                $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $message
                , 'disable_web_page_preview' => true);
                $bot->postSend('sendMessage', $resp);
                break;
        }

    } catch (Exception $e) {
        file_put_contents('dump.txt', 'test');
        $text = $e->getMessage();
        $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text);
        $bot->postSend('sendMessage', $resp);
    }
}
