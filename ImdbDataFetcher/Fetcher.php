<?php

namespace ImdbDataFetcher;

use DOMDocument;
use DOMXPath;

class Fetcher
{

    const URL = 'https://www.imdb.com/title/tt<id>/';

    protected $data = [];
    protected $url = '';
    protected $content;
    protected $movie_id;

    public function getUrl()
    {
        return str_replace('<id>', $this->getMovieId(), static::URL);
    }

    public function setMovieId($id)
    {
        $this->movie_id = $id;
    }

    public function getMovieId()
    {
        return $this->movie_id;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function process()
    {
        $this->fetchContent();
        $this->parseContent();
    }

    public function getData()
    {
        return $this->data;
    }

    public function getJSON()
    {
        return json_encode($this->getData());
    }

    public function setData($data = [])
    {
        $this->data = $data;
    }

    public function clearData()
    {
        $this->url = '';
        $this->content = '';
        $this->data = [];
    }

    public function createCurlHandler()
    {
        $handler = curl_init();
        $options = [
            CURLOPT_URL => $this->getUrl(),
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_POST           => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 30
        ];
        curl_setopt_array($handler, $options);
        return $handler;
    }

    public function fetchContent()
    {
        $handler = $this->createCurlHandler();
        $content = curl_exec($handler);
        curl_close($handler);
        $this->setContent($content);
    }

    public function parseContent()
    {
        $content = $this->getContent();
        $content = str_replace("\n", '', $content);
        $c = $this->getContent();
        $d = new DOMDocument();
        @$d->loadHTML($c);
        $xp = new DOMXPath($d);
        $jsonScripts = $xp->query('//script[@type="application/ld+json"]');
        $json = trim($jsonScripts->item(0)->nodeValue);
        $data = json_decode($json, true);
        $this->setData($data);
    }
}
