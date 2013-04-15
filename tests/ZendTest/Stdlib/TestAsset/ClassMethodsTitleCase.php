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

class ClassMethodsTitleCase
{
    protected $FooBar = '1';

    protected $FooBarBaz = '2';

    protected $IsFoo = true;

    protected $IsBar = true;

    protected $HasFoo = true;

    protected $HasBar = true;

    public function getFooBar()
    {
        return $this->FooBar;
    }

    public function setFooBar($value)
    {
        $this->FooBar = $value;
        return $this;
    }

    public function getFooBarBaz()
    {
        return $this->FooBarBaz;
    }

    public function setFooBarBaz($value)
    {
        $this->FooBarBaz = $value;
        return $this;
    }

    public function getIsFoo()
    {
        return $this->IsFoo;
    }

    public function setIsFoo($IsFoo)
    {
        $this->IsFoo = $IsFoo;
        return $this;
    }

    public function getIsBar()
    {
        return $this->IsBar;
    }

    public function setIsBar($IsBar)
    {
        $this->IsBar = $IsBar;
        return $this;
    }

    public function getHasFoo()
    {
        return $this->HasFoo;
    }

    public function getHasBar()
    {
        return $this->HasBar;
    }

    public function setHasFoo($HasFoo)
    {
        $this->HasFoo = $HasFoo;
        return $this;
    }

    public function setHasBar($HasBar)
    {
        $this->HasBar = $HasBar;
    }
}
