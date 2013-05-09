<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Di\TestAsset\PreferredImplClasses;

class E implements AAwareInterface
{
    public $a = null;
    public function foo(A $a)
    {
        $this->a = $a;
    }
}
