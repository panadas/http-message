<?php
namespace Panadas\HttpMessageModule;

use Panadas\HttpMessageModule\DataStructure\ResponseCookies;
use Panadas\HttpMessageModule\DataStructure\ResponseHeaders;

class Response
{

    private $charset;
    private $contentType;
    private $statusCode;
    private $headers;
    private $cookies;
    private $content;

    protected static $statusCodes = [
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",
        418 => "I\"m a Teapot",
        422 => "Unprocessable Entity",
        423 => "Locked",
        424 => "Failed Dependency",
        424 => "Method Failure",
        425 => "Unordered Collection",
        426 => "Upgrade Required",
        428 => "Precondition Required",
        429 => "Too Many Requests",
        431 => "Request Header Fields Too Large",
        449 => "Retry With",
        450 => "Blocked by Windows Parental Controls",
        451 => "Unavailable For Legal Reasons",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported"
    ];

    public function __construct(
        $charset = null,
        ResponseHeaders $headers = null,
        ResponseCookies $cookies = null,
        $content = null
    ) {
        if (null === $charset) {
            $charset = mb_internal_encoding();
        }

        if (null === $headers) {
            $headers = new ResponseHeaders();
        }

        if (null === $cookies) {
            $cookies = new ResponseCookies();
        }

        $this
            ->setCharset($charset)
            ->setHeaders($headers)
            ->setCookies($cookies)
            ->setContent($content)
            ->setStatusCode(200)
            ->setContentType("text/plain");
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    protected function setHeaders(ResponseHeaders $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    protected function setCookies(ResponseCookies $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function hasContent()
    {
        return (null !== $this->getContent());
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function removeContent()
    {
        return $this->setContent(null);
    }

    public function prependContent($content)
    {
        return $this->setContent($content . $this->getContent());
    }

    public function appendContent($content)
    {
        return $this->setContent($this->getContent() . $content);
    }

    public function send()
    {
        return $this
            ->sendHeaders()
            ->sendContent();
    }

    public function sendHeaders()
    {
        if (!headers_sent()) {

            $headers = [];

            $statusCode = $this->getStatusCode();
            $statusMessage = static::getStatusMessage($statusCode);

            $headers[] = "HTTP/1.1 {$statusCode} {$statusMessage}";
            $headers[] = "Content-Type: {$this->getContentType()}; charset={$this->getCharset()}";

            if ($this->getHeaders()->populated()) {
                $headers = array_merge($headers, explode("\n", $this->getHeaders()));
            }

            foreach ($headers as $header) {
                header($header, false);
            }

            foreach ($this->getCookies() as $cookie) {
                $cookie->send();
            }

        }

        return $this;
    }

    public function sendContent()
    {
        if ($this->hasContent()) {
            echo $this->getContent();
        }

        return $this;
    }

    public static function getStatusCodes()
    {
        return static::$statusCodes;
    }

    public static function hasStatusCode($statusCode)
    {
        return array_key_exists($statusCode, static::getStatusCodes());
    }

    public static function getStatusMessage($statusCode)
    {
        return static::hasStatusCode($statusCode) ? static::$statusCodes[$statusCode] : null;
    }
}
