<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter\Callback;
use Zend\Stdlib\CallbackHandler;

/**
 * @group      Zend_Paginator
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testMustDefineTwoCallbacksOnConstructor()
    {
        $itemsCallback = new CallbackHandler(function () {
            return array();
        });
        $countCallback = new CallbackHandler(function () {
            return 0;
        });
        $adapter = new Callback($itemsCallback, $countCallback);

        $this->assertAttributeSame($itemsCallback, 'itemsCallback', $adapter);
        $this->assertAttributeSame($countCallback, 'countCallback', $adapter);
    }

    public function testShouldAcceptAnyCallableOnConstructor()
    {
        $itemsCallback = function () {
            return range(1,  10);
        };
        $countCallback = 'rand';
        $adapter = new Callback($itemsCallback, $countCallback);

        $this->assertAttributeInstanceOf('Zend\Stdlib\CallbackHandler', 'itemsCallback', $adapter);
        $this->assertAttributeInstanceOf('Zend\Stdlib\CallbackHandler', 'countCallback', $adapter);
    }

    public function testMustRunItemCallbackToGetItems()
    {
        $data = range(1,  10);
        $itemsCallback = function () use ($data) {
            return $data;
        };
        $countCallback = function () {};
        $adapter = new Callback($itemsCallback, $countCallback);

        $this->assertSame($data, $adapter->getItems(0, 10));
    }

    public function testMustPassArgumentsToGetItemCallback()
    {
        $data = array(0, 1, 2, 3);
        $itemsCallback = function ($offset, $itemCountPerPage) {
            return range($offset, $itemCountPerPage);
        };
        $countCallback = function () {};
        $adapter = new Callback($itemsCallback, $countCallback);

        $this->assertSame($data, $adapter->getItems(0, 3));
    }

    public function testMustRunCountCallbackToCount()
    {
        $count = 1988;
        $itemsCallback = function () {};
        $countCallback = function () use ($count) {
            return $count;
        };
        $adapter = new Callback($itemsCallback, $countCallback);

        $this->assertSame($count, $adapter->count());
    }
}
