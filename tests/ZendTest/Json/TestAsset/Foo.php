<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Json\TestAsset;

/**
 * Class for testing JSON-RPC server
 */
class Foo
{
    /**
     * Bar
     *
     * @param  bool $one
     * @param  string $two
     * @param  mixed $three
     * @return array
     */
    public function bar($one, $two = 'two', $three = null)
    {
        return array($one, $two, $three);
    }

    /**
     * Baz
     *
     * @return void
     */
    public function baz()
    {
        throw new \Exception('application error');
    }
}
