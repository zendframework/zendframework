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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper.php
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/**
 * @see Zend_Tool_Framework_Action_Base
 */
require_once 'Zend/Tool/Framework/Action/Repository.php';
require_once 'Zend/Tool/Framework/Action/Base.php';

require_once '_files/Foo.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Action
 */
class Zend_Tool_Framework_Action_RepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Tool_Framework_Action_Repository
     */
    protected $_repository = null;

    public function setup()
    {
        $this->_repository = new Zend_Tool_Framework_Action_Repository();
    }

    public function teardown()
    {
        $this->_repository = null;
    }

    public function testRepositoryIsEmpty()
    {
        $this->assertEquals(0, count($this->_repository));
    }

    public function testAddActionCanHandleActionObjects()
    {
        $fooAction = new Zend_Tool_Framework_Action_Base();
        $fooAction->setName('Foo');
        $this->_repository->addAction($fooAction);

        $this->assertEquals(1, count($this->_repository));
        $this->assertEquals('Zend_Tool_Framework_Action_Base', get_class($this->_repository->getAction('Foo')));
    }

    public function testAddActionWillParseNameFromClassNameOnExtendedActions()
    {
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Foo());
        $this->assertEquals('Zend_Tool_Framework_Action_Foo', get_class($this->_repository->getAction('Foo')));
    }

    /**
     * @expectedException Zend_Tool_Framework_Action_Exception
     */
    public function testAddActionThrowsExceptionOnDuplicateNameAction()
    {
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Foo());
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Foo());
    }

    /**
     * @expectedException Zend_Tool_Framework_Action_Exception
     */
    public function testAddActionThrowsExceptionOnActionWithNoName()
    {
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Base());
    }

    public function testGetActionReturnsNullOnNonExistentAction()
    {
        $this->assertNull($this->_repository->getAction('Foo'));
    }

    public function testRepositoryIsCountable()
    {
        $this->assertTrue($this->_repository instanceof Countable);
    }

    public function testRepositoryIsIterable()
    {
        $this->assertTrue($this->_repository instanceof Traversable);
    }

    public function testRepositoryCanIterate()
    {
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Base('Foo'));
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Base('Bar'));
        $i=0;
        foreach ($this->_repository as $action) {
            $i++;
            $this->assertEquals('Zend_Tool_Framework_Action_Base', get_class($action));
        }
        $this->assertEquals(2, $i);
    }

    public function testGetActionsReturnsAnArrayOfActions()
    {
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Base('Foo'));
        $this->_repository->addAction(new Zend_Tool_Framework_Action_Base('Bar'));
        $i=0;
        foreach ($this->_repository->getActions() as $action) {
            $i++;
            $this->assertEquals('Zend_Tool_Framework_Action_Base', get_class($action));
        }
        $this->assertEquals(2, $i);
    }

    public function testProcessMethodReturnsNull()
    {
        $this->assertNull($this->_repository->process());
    }



}
