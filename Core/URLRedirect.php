<?php


namespace Core;


class URLRedirect
{
    private $rawURL ;
    private $redirectedURL;
    private $data;

    /**
     * URLRedirect constructor.
     * @param $rawURL URL to be followed
     */
    public function __construct($rawURL)
    {
        $this->rawURL = $rawURL;
        $this->URLRedirecting();
    }

    /**
     * @return string URL redirected to
     */
    public function getRedirectedURL() : string
    {
        return $this->redirectedURL;
    }

    /**
     * @return string Data received from URL
     */
    public function getData() : string
    {
        return $this->data;
    }

    function URLRedirecting()
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
        $this->data = curl_exec($ch);
        /* if(curl_errno($ch)){
            throw new Exception(curl_error($ch));
        } */
        // if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) throw new Exception('Can\'t get media.');
        $this->redirectedURL = curl_getinfo($ch)['url'];
        // another redirect_url get
        /* var_dump(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
        if (preg_match('~Location: (.*)~i', $result, $match)) {
            $location = trim($match[1]);
            var_dump($location);
        } */
        return;
    }


}