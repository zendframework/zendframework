<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
