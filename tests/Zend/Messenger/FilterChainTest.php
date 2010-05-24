<?php
/**
 * Phly - PHp LibrarY
 * 
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (C) 2008 - Present, Matthew Weier O'Phinney
 * @author     Matthew Weier O'Phinney <mweierophinney@gmail.com> 
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 */

namespace ZendTest\SignalSlot;
use Zend\SignalSlot\FilterChain,
    Zend\SignalSlot\Handler;

/**
 * @category   Phly
 * @package    Phly_PubSub
 * @subpackage Test
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
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

    public function testSubscribeShouldReturnHandler()
    {
        $handle = $this->filterchain->attach($this, __METHOD__);
        $this->assertTrue($handle instanceof Handler);
    }

    public function testSubscribeShouldAddHandlerToSubscribers()
    {
        $handler  = $this->filterchain->attach($this, __METHOD__);
        $handlers = $this->filterchain->getHandlers();
        $this->assertEquals(1, count($handlers));
        $this->assertContains($handler, $handlers);
    }

    public function testUnsubscribeShouldRemoveHandlerFromSubscribers()
    {
        $handle = $this->filterchain->attach($this, __METHOD__);
        $handles = $this->filterchain->getHandlers();
        $this->assertContains($handle, $handles);
        $this->filterchain->detach($handle);
        $handles = $this->filterchain->getHandlers();
        $this->assertNotContains($handle, $handles);
    }

    public function testUnsubscribeShouldReturnFalseIfHandlerDoesNotExist()
    {
        $handle1 = $this->filterchain->attach($this, __METHOD__);
        $this->filterchain->clearHandlers();
        $handle2 = $this->filterchain->attach($this, 'handleTestTopic');
        $this->assertFalse($this->filterchain->detach($handle1));
    }

    public function testRetrievingSubscribedHandlersShouldReturnEmptyArrayWhenNoSubscribersExist()
    {
        $handles = $this->filterchain->getHandlers();
        $this->assertTrue(empty($handles));
    }

    public function testFilterShouldPassReturnValueOfEachSubscriberToNextSubscriber()
    {
        $this->filterchain->attach('trim');
        $this->filterchain->attach('str_rot13');
        $value = $this->filterchain->filter(' foo ');
        $this->assertEquals(\str_rot13('foo'), $value);
    }

    public function testFilterShouldAllowMultipleArgumentsButFilterOnlyFirst()
    {
        $this->filterchain->attach($this, 'filterTestCallback1');
        $this->filterchain->attach($this, 'filterTestCallback2');
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
