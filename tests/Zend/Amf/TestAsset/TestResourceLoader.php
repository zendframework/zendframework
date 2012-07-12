<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset;

use Zend\Loader\PrefixPathMapper;

class TestResourceLoader implements PrefixPathMapper
{
    public $suffix;
    public $namespace = 'ZendTest\\Amf\\TestAsset\\';

    public function __construct($suffix)
    {
        $this->suffix = $suffix;
    }

    public function addPrefixPath($prefix, $path) {}
    public function removePrefixPath($prefix, $path = null) {}
    public function isLoaded($name) {}
    public function getClassName($name) {}

    public function load($name)
    {
        return $this->namespace . $name . $this->suffix;
    }
}
