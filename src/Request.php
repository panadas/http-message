<?php
namespace Panadas\HttpMessage;

use Panadas\HttpMessage\DataStructure\DataParams;
use Panadas\HttpMessage\DataStructure\QueryParams;
use Panadas\HttpMessage\DataStructure\RequestCookies;
use Panadas\HttpMessage\DataStructure\RequestHeaders;
use Panadas\HttpMessage\DataStructure\ServerParams;

class Request
{

    private $headers;
    private $serverParams;
    private $queryParams;
    private $dataParams;
    private $cookies;

    const METHOD_HEAD   = "HEAD";
    const METHOD_GET    = "GET";
    const METHOD_POST   = "POST";
    const METHOD_PUT    = "PUT";
    const METHOD_DELETE = "DELETE";

    const PARAM_METHOD = "_method";

    public function __construct(
        RequestHeaders $headers = null,
        ServerParams $serverParams = null,
        QueryParams $queryParams = null,
        DataParams $dataParams = null,
        RequestCookies $cookies = null
    ) {
        if (null === $headers) {
            $headers = new RequestHeaders();
        }

        if (null === $serverParams) {
            $headers = new ServerParams();
        }

        if (null === $queryParams) {
            $queryParams = new QueryParams();
        }

        if (null === $dataParams) {
            $dataParams = new DataParams();
        }

        if (null === $cookies) {
            $cookies = new RequestCookies();
        }

        $this
            ->setHeaders($headers)
            ->setServerParams($serverParams)
            ->setQueryParams($queryParams)
            ->setDataParams($dataParams)
            ->setCookies($cookies);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    protected function setHeaders(RequestHeaders $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    protected function setServerParams(ServerParams $serverParams)
    {
        $this->serverParams = $serverParams;

        return $this;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    protected function setQueryParams(QueryParams $queryParams)
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    public function getDataParams()
    {
        return $this->dataParams;
    }

    protected function setDataParams(DataParams $dataParams)
    {
        $this->dataParams = $dataParams;

        return $this;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    protected function setCookies(RequestCookies $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    public function getMethod()
    {
        $method = $this->getQueryParams()->get(
            static::PARAM_METHOD,
            $this->getDataParams()->get(
                static::PARAM_METHOD,
                $this->getServerParams()->get("REQUEST_METHOD", static::METHOD_GET)
            )
        );

        return mb_strtoupper($method);
    }

    public function isHead()
    {
        return ($this->getMethod() === static::METHOD_HEAD);
    }

    public function isGet()
    {
        return ($this->getMethod() === static::METHOD_GET);
    }

    public function isPost()
    {
        return ($this->getMethod() === static::METHOD_POST);
    }

    public function isPut()
    {
        return ($this->getMethod() === static::METHOD_PUT);
    }

    public function isDelete()
    {
        return ($this->getMethod() === static::METHOD_DELETE);
    }

    public function isSecure()
    {
        $headers = [
            "HTTPS" => "ON",
            "HTTP_X_FORWARDED_PROTO" => "HTTPS"
        ];

        $serverParams = $this->getServerParams();

        foreach ($headers as $name => $value) {
            if (mb_strtoupper($serverParams->get($name)) === $value) {
                return true;
            }
        }

        return false;
    }

    public function isAjax()
    {
        return ($this->getHeaders()->get("X-Requested-With") === "XMLHttpRequest");
    }

    public function getIp()
    {
        $headers = [
            "HTTP_CLIENT_IP",
            "HTTP_X_FORWARDED_FOR",
            "REMOTE_ADDR"
        ];

        $serverParams = $this->getServerParams();

        foreach ($headers as $name) {
            if (!$serverParams->has($name)) {
                continue;
            }

            $value = $serverParams->get($name);

            if (mb_strpos($value, ",") !== false) {
                $value = preg_split("\s*,\s*", $value)[0];
            }

            return $value;
        }

        return null;
    }

    public function getUri($absolute = true, $query = true)
    {
        $uri = null;

        $serverParams = $this->getServerParams();

        if ($absolute) {

            $secure = $this->isSecure();

            $uri .= $secure ? "https://" : "http://";
            $uri .= $serverParams->get("HTTP_HOST");

            $port = (int) $serverParams->get("SERVER_PORT");
            if (($secure && (443 !== $port)) || (!$secure && (80 !== $port))) {
                $uri .= ":{$port}";
            }

        }

        $uri .= $serverParams->get("REQUEST_URI");

        if (!$query) {
            $pos = mb_strpos($uri, "?");
            if (false !== $pos) {
                $uri = mb_substr($uri, 0, $pos);
            }
        }

        return $uri;
    }

    public static function createFromRequest()
    {
        return new static(
            (new RequestHeaders())->replace(apache_request_headers()),
            (new ServerParams)->bind($_SERVER),
            (new QueryParams)->bind($_GET),
            (new DataParams)->bind($_POST),
            (new RequestCookies)->bind($_COOKIE)
        );
    }
}
