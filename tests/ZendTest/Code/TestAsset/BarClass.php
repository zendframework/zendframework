<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\TestAsset;

include __DIR__ . '/foo/bar/baz.php';

use A\B\C;
use Foo\Bar\Baz as FooBarBaz;

abstract class BarClass
{
    const BAR = 5;
    const FOO = self::BAR;

    protected static $bar = 'value';

    final public function one()
    {
        // foo
    }

    protected function two(\ArrayObject $o = null)
    {
        // two
    }

    protected function three(\ArrayObject $o, &$t = 2, FooBarBaz\BazBarFoo $bbf = self::BAR)
    {
        $x = 5 + 5;
        $y = 'this string';
    }

}
