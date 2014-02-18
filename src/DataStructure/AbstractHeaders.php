<?php
namespace Panadas\HttpMessage\DataStructure;

use Panadas\DataStructure\Hash;
use Panadas\Util\Php;

abstract class AbstractHeaders extends Hash
{

    public function __toString()
    {
        $lines = [];

        foreach ($this->sort() as $key => $values) {

            $key = implode("-", array_map("ucfirst", explode("-", $key)));

            if (null === $values) {
                $lines[] = $key;
                continue;
            }

            if (!Php::isIterable($values)) {
                $values = (array) $values;
            }

            foreach ($values as $value) {
                $lines[] = "{$key}: {$value}";
            }

        }

        return implode("\n", $lines);
    }

    protected function filter(&$key, &$value = null)
    {
        $key = strtr(mb_strtolower($key), "_", "-");
    }
}
