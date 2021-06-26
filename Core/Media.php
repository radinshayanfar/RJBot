<?php


namespace Core;

use Exception;
use Sendable;

include_once('URLRedirect.php');


abstract class Media implements Sendable
{
    protected $id;
    protected $url;
    protected $hostFetchURL;
    protected $host;
    protected $links = array();

    /**
     * Media constructor.
     * @param $url URLRedirect redirect followed URL
     * @throws Exception If failed to get host
     */
    protected function __construct($url)
    {
        $this->url = $url;

    }

    /**
     * Processes media ID and assign it to $id
     */
    protected function processID()
    {
        $path_parts = explode('/', parse_url($this->url->getRedirectedURL(), PHP_URL_PATH));
        $this->id = end($path_parts);
    }

    /**
     * Processes media host and assign it to $host
     * @throws Exception If failed to fetch host
     */
    protected function processHost()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.radiojavan.com' . $this->hostFetchURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "id=$this->id");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0');
        $result = curl_exec($ch);
        if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) throw new Exception('TimeOut error occurred. Please try again in a moment.');
        if (curl_errno($ch)) throw new Exception('Unknown error occurred. Please try again in a moment.');
//        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) throw new Exception('Unknown error occurred. Please try again in a moment');
        curl_close($ch);
        $this->host = json_decode($result, true)['host'];
        if ($this->host === null) {
            throw new Exception('Unable to get host address. Try again later.');
        }
    }

    /**
     * @return array Links of media
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * Generates media download url and put in links array
     */
    abstract protected function generateLinks();

}