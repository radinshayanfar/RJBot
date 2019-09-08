<?php


namespace Core;


use Exception;

class URLRedirect
{
    private $rawURL;
    private $redirectedURL;
    private $data;

    /**
     * URLRedirect constructor.
     * @param $rawURL string URL to be followed
     * @throws Exception If curl error occurred
     */
    public function __construct($rawURL, $headers = null)
    {
        $this->rawURL = $rawURL;
        $this->URLRedirecting($headers);
    }

    /**
     * @return string URL redirected to
     */
    public function getRedirectedURL(): string
    {
        return $this->redirectedURL;
    }

    /**
     * @return string Data received from URL
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Follows $rawURL redirects upto 3 times
     * Puts retrieved data to $data
     * Puts last url to $redirectedURL
     * @throws Exception If curl error occurred
     */
    private function URLRedirecting($headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->rawURL);
        curl_setopt($ch, CURLOPT_HEADER, true);
        // curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        if (is_array($headers))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->data = curl_exec($ch);
        if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) throw new Exception('TimeOut error occurred. Please try again in a moment.');
        if (curl_errno($ch)) throw new Exception('Unknown error occurred. Please try again in a moment.');
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) throw new Exception('Can\'t get media.');
        $this->redirectedURL = curl_getinfo($ch)['url'];
        return;
    }


}