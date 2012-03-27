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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\FlashMessenger,
    ZendTest\Session\TestAsset\TestManager as SessionManager;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FlashMessengerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->session = new SessionManager();
        $this->helper  = new FlashMessenger();
        $this->helper->setSessionManager($this->session);
    }

    public function seedMessages()
    {
        $helper = new FlashMessenger();
        $helper->setSessionManager($this->session);
        $helper->addMessage('foo');
        $helper->addMessage('bar');
        unset($helper);
    }

    public function testComposesSessionManagerByDefault()
    {
        $helper  = new FlashMessenger();
        $session = $helper->getSessionManager();
        $this->assertInstanceOf('Zend\Session\SessionManager', $session);
    }

    public function testSessionManagerIsMutable()
    {
        $session = new SessionManager();
        $this->helper->setSessionManager($session);
        $this->assertSame($session, $this->helper->getSessionManager());
    }

    public function testUsesContainerNamedAfterClass()
    {
        $container = $this->helper->getContainer();
        $this->assertEquals('FlashMessenger', $container->getName());
    }

    public function testUsesNamespaceNamedDefaultWithNoConfiguration()
    {
        $this->assertEquals('default', $this->helper->getNamespace());
    }

    public function testNamespaceIsMutable()
    {
        $this->helper->setNamespace('foo');
        $this->assertEquals('foo', $this->helper->getNamespace());
    }

    public function testMessengerIsEmptyByDefault()
    {
        $this->assertFalse($this->helper->hasMessages());
    }

    public function testCanAddMessages()
    {
        $this->helper->addMessage('foo');
        $this->assertTrue($this->helper->hasCurrentMessages());
    }

    public function testAddingMessagesDoesNotChangeCount()
    {
        $this->assertEquals(0, count($this->helper));
        $this->helper->addMessage('foo');
        $this->assertEquals(0, count($this->helper));
    }

    public function testCanClearMessages()
    {
        $this->seedMessages();
        $this->assertTrue($this->helper->hasMessages());
        $this->helper->clearMessages();
        $this->assertFalse($this->helper->hasMessages());
    }

    public function testCanRetrieveMessages()
    {
        $this->seedMessages();
        $this->assertTrue($this->helper->hasMessages());
        $messages = $this->helper->getMessages();
        $this->assertEquals(2, count($messages));
        $this->assertContains('foo', $messages);
        $this->assertContains('bar', $messages);
    }

    public function testCanRetrieveCurrentMessages()
    {
        $this->helper->addMessage('foo');
        $messages = $this->helper->getCurrentMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('foo', $messages);
    }

    public function testCanClearCurrentMessages()
    {
        $this->helper->addMessage('foo');
        $this->assertTrue($this->helper->hasCurrentMessages());
        $this->helper->clearCurrentMessages();
        $this->assertFalse($this->helper->hasCurrentMessages());
    }

    public function testIterationOccursOverMessages()
    {
        $this->seedMessages();
        $test = array();
        foreach ($this->helper as $message) {
            $test[] = $message;
        }
        $this->assertEquals(array('foo', 'bar'), $test);
    }

    public function testCountIsOfMessages()
    {
        $this->seedMessages();
        $this->assertEquals(2, count($this->helper));
    }
}
