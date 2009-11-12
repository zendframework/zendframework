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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Controller_Action_HelperBrokerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_HelperBrokerTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
require_once 'Zend/Controller/Action/Helper/Redirector.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_HelperBroker_PriorityStackTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Controller_Action_HelperBroker_PriorityStack
     */
    public $stack = null;

    public function setUp()
    {
        $this->stack = new Zend_Controller_Action_HelperBroker_PriorityStack();
    }

    public function testStackMaintainsLifo()
    {
        $this->stack->push(new Zend_Controller_Action_Helper_ViewRenderer());
        $this->stack->push(new Zend_Controller_Action_Helper_Redirector());
        $this->assertEquals(2, count($this->stack));
        $iterator = $this->stack->getIterator();
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', get_class(current($iterator)));
        next($iterator);
        $this->assertEquals('Zend_Controller_Action_Helper_ViewRenderer', get_class(current($iterator)));
    }

    public function testStackPrioritiesWithDefaults()
    {
        $this->stack->push(new Zend_Controller_Action_Helper_ViewRenderer());
        $this->stack->push(new Zend_Controller_Action_Helper_Redirector());
        $this->assertEquals(3, $this->stack->getNextFreeHigherPriority());
        $this->assertEquals(0, $this->stack->getNextFreeLowerPriority());
        $this->assertEquals(2, $this->stack->getHighestPriority());
        $this->assertEquals(1, $this->stack->getLowestPriority());
    }


    public function testStackMaintainsReturnsCorrectNextPriorityWithSetPriorities()
    {
        $this->stack->offsetSet(10, new Zend_Controller_Action_Helper_ViewRenderer());
        $this->stack->offsetSet(11, new Zend_Controller_Action_Helper_Redirector());
        $this->assertEquals(12, $this->stack->getNextFreeHigherPriority(10));
        $this->assertEquals(9, $this->stack->getNextFreeLowerPriority(10));
        $this->assertEquals(11, $this->stack->getHighestPriority());
        $this->assertEquals(10, $this->stack->getLowestPriority());
    }

    public function testStackMaintainsReturnsCorrectNextPriorityWithSetPrioritiesSplit()
    {
        $this->stack->offsetSet(10, new Zend_Controller_Action_Helper_ViewRenderer());
        $this->stack->offsetSet(20, new Zend_Controller_Action_Helper_Redirector());
        $this->assertEquals(11, $this->stack->getNextFreeHigherPriority(10));
        $this->assertEquals(9, $this->stack->getNextFreeLowerPriority(10));

        $this->assertEquals(11, $this->stack->getNextFreeHigherPriority(11));
        $this->assertEquals(11, $this->stack->getNextFreeLowerPriority(11));

        $this->assertEquals(21, $this->stack->getNextFreeHigherPriority(20));
        $this->assertEquals(19, $this->stack->getNextFreeLowerPriority(20));

        $this->assertEquals(20, $this->stack->getHighestPriority());
        $this->assertEquals(10, $this->stack->getLowestPriority());
    }

    public function testStackAccessors()
    {
        $this->stack->push(new Zend_Controller_Action_Helper_ViewRenderer());
        $this->stack->push(new Zend_Controller_Action_Helper_Redirector());
        unset($this->stack->ViewRenderer);
        $this->assertEquals(1, count($this->stack));
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', get_class(current($this->stack->getIterator())));
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', get_class($this->stack->Redirector));
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', get_class($this->stack->offsetGet('Redirector')));
        $this->assertEquals('Zend_Controller_Action_Helper_Redirector', get_class($this->stack->offsetGet(2)));
    }

}
