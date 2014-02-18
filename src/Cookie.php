<?php
namespace Panadas\HttpMessage;

class Cookie
{

    private $name;
    private $value;
    private $expires;
    private $path;
    private $domain;
    private $secure = false;
    private $httpOnly = false;

    public function __construct(
        $name,
        $value = null,
        \DateTime $expires = null,
        $path = null,
        $domain = null,
        $secure = false,
        $httpOnly = false
    ) {
        $this
            ->setName($name)
            ->setValue($value)
            ->setExpires($expires)
            ->setPath($path)
            ->setDomain($domain)
            ->setSecure($secure)
            ->setHttpOnly($httpOnly);
    }

    public function __toString()
    {
        $parts = [];

        $name = urlencode($this->getName());
        $expires = $this->getExpires();

        if (!$this->isDelete()) {
            $value = urlencode($this->getValue());
        } else {
            $value = "deleted";
            if ((null === $expires) || ($expires > new \DateTime())) {
                $expires = new \DateTime("-1 year");
            }
        }

        $parts[] = "{$name}={$value}";

        if (null !== $expires) {
            $parts[] = "expires={$expires->format("r")}";
        }

        if ($this->hasPath()) {
            $parts[] = "path={$this->getPath()}";
        }

        if ($this->hasDomain()) {
            $parts[] = "domain={$this->getDomain()}";
        }

        if ($this->isSecure()) {
            $parts[] = "secure";
        }

        if ($this->isHttpOnly()) {
            $parts[] = "httponly";
        }

        return implode("; ", $parts);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function hasValue()
    {
        return (null !== $this->getValue());
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function removeValue()
    {
        return $this->setValue(null);
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function hasExpires()
    {
        return (null !== $this->getExpires());
    }

    public function setExpires(\DateTime $expires = null)
    {
        if (null !== $expires) {
            $expires->setTimezone(new \DateTimeZone("UTC"));
        }

        $this->expires = $expires;

        return $this;
    }

    public function removeExpires()
    {
        return $this->setExpires(null);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function hasPath()
    {
        return (null !== $this->getPath());
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function removePath()
    {
        return $this->setPath(null);
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function hasDomain()
    {
        return (null !== $this->getDomain());
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    public function removeDomain()
    {
        return $this->setDomain(null);
    }

    public function isSecure()
    {
        return $this->secure;
    }

    public function setSecure($secure)
    {
        $this->secure = (bool) $secure;

        return $this;
    }

    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = (bool) $httpOnly;

        return $this;
    }

    public function isDelete()
    {
        return (!$this->hasValue() || ($this->hasExpires() && ($this->getExpires() < new \DateTime())));
    }

    public function send()
    {
        if (!headers_sent()) {
            header("Set-Cookie: {$this}");
        }

        return $this;
    }
}
