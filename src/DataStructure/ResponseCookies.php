<?php
namespace Panadas\HttpMessage\DataStructure;

use Panadas\DataStructure\Hash;
use Panadas\HttpMessage\Cookie;

class ResponseCookies extends Hash
{

    protected function filter(&$key, &$value = null)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof Cookie) {
            $value = new Cookie($key, $value);
        } elseif ($key !== $value->getName()) {
            $key = $value->getName();
        }
    }

    public function add(Cookie $cookie)
    {
        return $this->set($cookie->getName(), $cookie);
    }
}
