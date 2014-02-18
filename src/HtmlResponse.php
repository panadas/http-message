<?php
namespace Panadas\HttpMessage;

use Panadas\HttpMessage\DataStructure\Headers;
use Panadas\HttpMessage\DataStructure\ResponseCookies;

class HtmlResponse extends Response
{

    public function __construct($charset = null, Headers $headers = null, ResponseCookies $cookies = null)
    {
        parent::__construct($charset, $headers, $cookies);

        $this->setContentType("text/html");
    }

    public function prependContent($content)
    {
        $existingContent = $this->getContent();

        if (false === mb_strpos($existingContent, "<body>", null, $this->getCharset())) {
            return parent::prependContent($content);
        }

        return $this->setContent(str_replace("<body>", "<body>{$content}", $existingContent));
    }

    public function appendContent($content)
    {
        $existingContent = $this->getContent();

        if (false === mb_strpos($existingContent, "</body>", null, $this->getCharset())) {
            return parent::appendContent($content);
        }

        return $this->setContent(str_replace("</body>", "{$content}</body>", $existingContent));
    }

    public function esc($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, $this->getCharset());
    }

    public function escAttr($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, $this->getCharset());
    }
}
