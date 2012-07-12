<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\TestAsset;

class ClassMethodsUnderscore
{
    protected $foo_bar = '1';

    protected $foo_bar_baz = '2';

    public function getFooBar()
    {
        return $this->foo_bar;
    }

    public function setFooBar($value)
    {
        $this->foo_bar = $value;
        return $this;
    }

    public function getFooBarBaz()
    {
        return $this->foo_bar_baz;
    }

    public function setFooBarBaz($value)
    {
        $this->foo_bar_baz = $value;
        return $this;
    }
}
