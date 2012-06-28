<?php

namespace ZendTest\Config\Reader\TestAssets;

use Zend\Config\Reader\ReaderInterface;
use Zend\Config\Exception;

class DummyReader implements ReaderInterface
{
    public function fromFile($filename)
    {
        if (!is_readable($filename)) {
            throw new Exception\RuntimeException("File '{$filename}' doesn't exist or not readable");
        }

        return unserialize(file_get_contents($filename));
    }

    public function fromString($string)
    {
        if (empty($string)) {
            return array();
        }

        return unserialize($string);
    }
}
