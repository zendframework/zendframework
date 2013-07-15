<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace ZendTest\Di\TestAsset\ConstructorInjection;

use ZendTest\Di\TestAsset\DummyInterface;

class D
{
    protected $d = null;
    public function __construct(DummyInterface $d)
    {
        $this->d = $d;
    }
}
