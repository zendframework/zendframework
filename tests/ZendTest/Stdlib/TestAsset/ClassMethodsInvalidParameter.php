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

class ClassMethodsInvalidParameter
{
    public function hasAlias($alias)
    {
        return $alias;
    }

    public function getTest($foo)
    {
        return $foo;
    }

    public function isTest($bar)
    {
        return $bar;
    }

    public function hasBar()
    {
        return true;
    }

    public function getFoo()
    {
        return "Bar";
    }

    public function isBla()
    {
        return false;
    }
}
