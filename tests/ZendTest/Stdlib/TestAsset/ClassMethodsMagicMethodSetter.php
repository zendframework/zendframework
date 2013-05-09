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

class ClassMethodsMagicMethodSetter
{
    protected $foo;

    public function __call($method, $args)
    {
        if(strlen($method) > 3 && strtolower(substr($method, 3)) == 'foo') {
            $this->foo = $args[0];
        }
    }

    public function getFoo()
    {
        return $this->foo;
    }
}
