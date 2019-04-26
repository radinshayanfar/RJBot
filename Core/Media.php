<?php


namespace Core;


abstract class Media
{
    protected $id;
    protected $url;
    protected $host;
    protected $links = array();

    /**
     * Media constructor.
     * @param $url URLRedirect redirect followed URL
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

    }

    /**
     * Processes media host and assign it to $host
     */
    protected function processHost()
    {

    }

    /**
     * @return array Links of media
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    abstract protected function generateLinks();


}