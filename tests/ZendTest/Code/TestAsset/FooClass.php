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
use A\B\C\D as E;
use Foo\Bar\Baz as FooBarBaz;

abstract class FooClass implements \ArrayAccess, E\Blarg, Local\SubClass
{
    const BAR = 5;
    const FOO = self::BAR;

    /**
     * Constant comment
     */
    const BAZ = 'baz';

    protected static $bar = 'value';
    public $foo = 'value2';

    /**
     * Test comment
     *
     * @var int
     */
    private $baz = 3;

    final public function fooBarBaz()
    {
        // foo
    }

}
