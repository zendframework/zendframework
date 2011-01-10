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
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\Stdlib;
use Zend\SignalSlot\FilterChain,
    Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->filterchain = new FilterChain;
    }

    public function testSubscribeShouldReturnCallbackHandler()
    {
        $handle = $this->filterchain->connect($this, __METHOD__);
        $this->assertTrue($handle instanceof CallbackHandler);
    }

    public function testSubscribeShouldAddCallbackHandlerToSubscribers()
    {
        $handler  = $this->filterchain->connect($this, __METHOD__);
        $handlers = $this->filterchain->getFilters();
        $this->assertEquals(1, count($handlers));
        $this->assertContains($handler, $handlers);
    }

    public function testUnsubscribeShouldRemoveCallbackHandlerFromSubscribers()
    {
        $handle = $this->filterchain->connect($this, __METHOD__);
        $handles = $this->filterchain->getFilters();
        $this->assertContains($handle, $handles);
        $this->filterchain->detach($handle);
        $handles = $this->filterchain->getFilters();
        $this->assertNotContains($handle, $handles);
    }

    public function testUnsubscribeShouldReturnFalseIfCallbackHandlerDoesNotExist()
    {
        $handle1 = $this->filterchain->connect($this, __METHOD__);
        $this->filterchain->clearFilters();
        $handle2 = $this->filterchain->connect($this, 'handleTestTopic');
        $this->assertFalse($this->filterchain->detach($handle1));
    }

    public function testRetrievingSubscribedFiltersShouldReturnEmptyArrayWhenNoSubscribersExist()
    {
        $handles = $this->filterchain->getFilters();
        $this->assertTrue(empty($handles));
    }

    public function testFilterShouldPassReturnValueOfEachSubscriberToNextSubscriber()
    {
        $this->filterchain->connect('trim');
        $this->filterchain->connect('str_rot13');
        $value = $this->filterchain->filter(' foo ');
        $this->assertEquals(\str_rot13('foo'), $value);
    }

    public function testFilterShouldAllowMultipleArgumentsButFilterOnlyFirst()
    {
        $this->filterchain->connect($this, 'filterTestCallback1');
        $this->filterchain->connect($this, 'filterTestCallback2');
        $obj = (object) array('foo' => 'bar', 'bar' => 'baz');
        $value = $this->filterchain->filter('', $obj);
        $this->assertEquals('foo:bar;bar:baz;', $value);
        $this->assertEquals((object) array('foo' => 'bar', 'bar' => 'baz'), $obj);
    }

    public function handleTestTopic($message)
    {
        $this->message = $message;
    }

    public function evaluateStringCallback($value)
    {
        return (!$value);
    }

    public function filterTestCallback1($string, $object)
    {
        if (isset($object->foo)) {
            $string .= 'foo:' . $object->foo . ';';
        }
        return $string;
    }

    public function filterTestCallback2($string, $object)
    {
        if (isset($object->bar)) {
            $string .= 'bar:' . $object->bar . ';';
        }
        return $string;
    }
}
