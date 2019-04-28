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
    if (!URLHelper::isHostEquals($url->getRedirectedURL(), 'www.radiojavan.com')) throw new Exception('Not a RadioJavan link.');
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
    global $db;

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
        $mediaType = '';
        if (URLHelper::validateURL($originalText))
            $mediaType = determineType($originalText);
        else
            throw new Exception('Can\'t get media.');
        $url = new URLRedirect($originalText);
        $caption = '@RJ_DownloadBot';
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
            $media = new Album($url, $db->autoIncrementStart());
        }
        $media->send($bot, $update, $caption);
    } catch (Exception $e) {
        $text = $e->getMessage();
        $resp = array('chat_id' => $chat_id, 'reply_to_message_id' => $message_id, 'text' => $text);
        $bot->postSend('sendMessage', $resp);
    }
}
