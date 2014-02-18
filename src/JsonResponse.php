<?php
namespace Panadas\HttpMessageModule;

use Panadas\HttpMessageModule\DataStructure\Headers;
use Panadas\HttpMessageModule\DataStructure\ResponseCookies;

class JsonResponse extends Response
{

    public function __construct($charset = null, Headers $headers = null, ResponseCookies $cookies = null)
    {
        parent::__construct($charset, $headers, $cookies);

        $this->setContentType("application/json");
    }

    public function getData($options = null, $depth = 512)
    {
        return $this->decode($this->getContent(), $options, $depth);
    }

    public function hasData()
    {
        return $this->hasContent();
    }

    public function setData($data, $options = null, $depth = 512)
    {
        if (null === $data) {
            return $this->removeContent();
        }

        return $this->setContent($this->encode($data, $options, $depth));
    }

    public function removeData()
    {
        return $this->setData(null);
    }

    public function prependContent($content)
    {
        throw new \RuntimeException("Cannot prepend to JSON content");
    }

    public function appendContent($content)
    {
        throw new \RuntimeException("Cannot append to JSON content");
    }

    public function encode($content, $options = null, $depth = 512)
    {
        return json_encode($content, $options, $depth);
    }

    public function decode($content, $options = null, $depth = 512)
    {
        return json_decode($content, true, $depth, $options);
    }
}
