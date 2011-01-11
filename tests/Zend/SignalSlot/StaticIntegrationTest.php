<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_SignalSlot
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\SignalSlot;

use Zend\SignalSlot\SignalSlot,
    Zend\SignalSlot\StaticSignalSlot,
    PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_SignalSlot
 * @subpackage UnitTests
 * @group      Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticIntegrationTest extends TestCase
{
    public function setUp()
    {
        StaticSignalSlot::resetInstance();
    }

    public function testCanConnectStaticallyToClassWithSignals()
    {
        $counter = (object) array('count' => 0);
        StaticSignalSlot::getInstance()->connect(
            'ZendTest\SignalSlot\TestAsset\ClassWithSignals', 
            'foo', 
            function ($context, array $params) use ($counter) {
                $counter->count++;
            }
        );
        $class = new TestAsset\ClassWithSignals();
        $class->foo();
        $this->assertEquals(1, $counter->count);
    }

    public function testLocalSlotsAreExecutedPriorToStaticSlots()
    {
        $test = (object) array('results' => array());
        StaticSignalSlot::getInstance()->connect(
            'ZendTest\SignalSlot\TestAsset\ClassWithSignals', 
            'foo', 
            function ($context, array $params) use ($test) {
                $test->results[] = 'static';
            }
        );
        $class = new TestAsset\ClassWithSignals();
        $class->signals()->connect('foo', function ($context, array $params) use ($test) {
            $test->results[] = 'local';
        });
        $class->foo();
        $this->assertEquals(array('local', 'static'), $test->results);
    }

    public function testLocalSlotsAreExecutedPriorToStaticSlotsRegardlessOfPriority()
    {
        $test = (object) array('results' => array());
        StaticSignalSlot::getInstance()->connect(
            'ZendTest\SignalSlot\TestAsset\ClassWithSignals', 
            'foo', 
            function ($context, array $params) use ($test) {
                $test->results[] = 'static';
            },
            10000 // high priority
        );
        $class = new TestAsset\ClassWithSignals();
        $class->signals()->connect('foo', function ($context, array $params) use ($test) {
            $test->results[] = 'local';
        }, 1); // low priority
        $class->signals()->connect('foo', function ($context, array $params) use ($test) {
            $test->results[] = 'local2';
        }, 1000); // medium priority
        $class->signals()->connect('foo', function ($context, array $params) use ($test) {
            $test->results[] = 'local3';
        }, 15000); // highest priority
        $class->foo();
        $this->assertEquals(array('local3', 'local2', 'local', 'static'), $test->results);
    }
}
