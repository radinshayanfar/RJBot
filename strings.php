<?php

$_STR = [
    "ERRORS" => [
        "no_rj" => "Not a RadioJavan link.",
        "unsupported_media" => "Unsupported media type. Currently supported media types are:
Music, Album, Podcast, and Video.",
        "timeout" => "TimeOut error occurred. Please try again in a moment.",
        "unknown" => "Unknown error occurred. Please try again in a moment.",
        "get_host_error" => "Unable to get host address. Try again later.",
        "cant_get" => "Can't get media.",
        "expired" => "Search results has been expired. Please resend the link to extract tracks.",
    ],

    "COMMANDS" => [
        "start" => "Hey %s!
You can search and download RadioJavan's media via this bot. Send /help to get help.", // Parameter: user's name

        "help" => "To get a media you can:
- send the media's link from RadioJavan's website or share media from RadioJavan's application to this bot, or
- send the phrase you want to search to this bot.  

Currently supported media types are:
Music, Album, Podcast, and Video."
    ],

    "no_result" => "Nothing found :(

Please note:
1- Keywords must be exactly typed as RadioJavan's.
2- Media can only be searched by its name, not lyrics.
3- Keywords are in English characters.",

    "not_text" => "Please send a link or a text to search.",

    "sending" => "Sending. Please wait...",
];
