<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\TestAsset;

class ClassMethodsUnderscore
{
    protected $foo_bar = '1';

    protected $foo_bar_baz = '2';

    protected $is_foo = true;

    protected $is_bar = true;

    protected $has_foo = true;

    protected $has_bar = true;

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

    public function getIsFoo()
    {
        return $this->is_foo;
    }

    public function setIsFoo($value)
    {
        $this->is_foo = $value;
        return $this;
    }

    public function isBar()
    {
        return $this->is_bar;
    }

    public function setIsBar($value)
    {
        $this->is_bar = $value;
        return $this;
    }

    public function getHasFoo()
    {
        return $this->has_foo;
    }

    public function setHasFoo($value)
    {
        $this->has_foo = $value;
        return $this;
    }

    public function hasBar()
    {
        return $this->has_bar;
    }

    public function setHasBar($value)
    {
        $this->has_bar = $value;
        return $this;
    }
}
