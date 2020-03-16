<?php


class TelegramAPI
{
    private $token;

    /**
     * TelegramAPI constructor.
     * @param $token string Telegram API Token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @param $method string Method
     * @param array $action Method's parameters
     */
    function webhookSend($method, $action = array())
    {
        // Checking if $action is array or not.
        if (!is_array($action)) {
            error_log("\$action is not array. It\'s " . gettype($action) . '.');
            exit;
        }

        // Adding method to $action
        $action['method'] = $method;

        // Sending via webhook
        echo json_encode($action);
    }


    /**
     * @param $method string Method
     * @param array $action Method's parameters
     * @return bool|mixed|string
     */
    function postSend($method, $action = array())
    {
        define("API_URL", 'https://api.telegram.org/bot' . $this->token . '/');

        foreach ($action as $key => &$val) {
            // encoding to JSON array action, for example reply_markup
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
        }
        $url = API_URL . $method . '?' . http_build_query($action);
        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 10);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, false);

        $response = curl_exec($handle);

        if ($response === false) {
            $errno = curl_errno($handle);
            $error = curl_error($handle);
            error_log("Curl returned error $errno: $error\n");
            curl_close($handle);
            return false;
        }

        $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);

        if ($http_code != 200) {
            error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            return false;
        }

        $response = json_decode($response, true);

        if (isset($response['description'])) {
            error_log($response['description'] . "\n");
        }

        //$response = $response['result'];

        return $response;
    }


}