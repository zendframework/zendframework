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
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\EventManager\EventManager,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\View\CreateViewModelFromArrayListener,
    Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CreateViewModelFromArrayListenerTest extends TestCase
{
    public function setUp()
    {
        $this->listener   = new CreateViewModelFromArrayListener();
        $this->event      = new MvcEvent();
    }

    public function testReCastsAssocArrayEventResultAsViewModel()
    {
        $array = array(
            'foo' => 'bar',
        );
        $this->event->setResult($array);
        $this->listener->createViewModelFromArray($this->event);

        $test = $this->event->getResult();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $test);
        $this->assertEquals($array, $test->getVariables());
    }

    public function nonAssocArrayResults()
    {
        return array(
            array(null),
            array(false),
            array(true),
            array(0),
            array(1),
            array(0.00),
            array(1.00),
            array('string'),
            array(array('foo', 'bar')),
            array(new stdClass),
        );
    }

    /**
     * @dataProvider nonAssocArrayResults
     */
    public function testDoesNotCastNonAssocArrayEventResults($test)
    {
        $this->event->setResult($test);

        $this->listener->createViewModelFromArray($this->event);

        $result = $this->event->getResult();
        $this->assertEquals(gettype($test), gettype($result));
        $this->assertEquals($test, $result);
    }

    public function testAttachesListenerAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->listener);
        $listeners = $events->getListeners('dispatch');

        $expectedCallback = array($this->listener, 'createViewModelFromArray');
        $expectedPriority = -80;
        $found            = false;
        foreach ($listeners as $listener) {
            $callback = $listener->getCallback();
            if ($callback === $expectedCallback) {
                if ($listener->getMetadatum('priority') == $expectedPriority) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Listener not found');
    }

    public function testDetachesListeners()
    {
        $events = new EventManager();
        $events->attachAggregate($this->listener);
        $listeners = $events->getListeners('dispatch');
        $this->assertEquals(1, count($listeners));
        $events->detachAggregate($this->listener);
        $listeners = $events->getListeners('dispatch');
        $this->assertEquals(0, count($listeners));
    }

    public function testViewModelCreatesViewModelWithEmptyArray()
    {
        $this->event->setResult(array());
        $this->listener->createViewModelFromArray($this->event);
        $result = $this->event->getResult();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }
}
