<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\FlashMessenger;
use ZendTest\Mvc\TestAsset\SessionManager;

class FlashMessengerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->session = new SessionManager();
        $this->helper  = new FlashMessenger();
        $this->helper->setSessionManager($this->session);
    }

    public function tearDown()
    {
        $this->session->getStorage()->clear();
    }

    public function seedMessages()
    {
        $helper = new FlashMessenger();
        $helper->setSessionManager($this->session);
        $helper->addMessage('foo');
        $helper->addMessage('bar');
        $helper->addInfoMessage('bar-info');
        $helper->addSuccessMessage('bar-success');
        $helper->addErrorMessage('bar-error');
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
        $this->assertFalse($this->helper->hasMessages(FlashMessenger::NAMESPACE_INFO));
    }

    public function testCanAddMessages()
    {
        $this->helper->addMessage('foo');
        $this->assertTrue($this->helper->hasCurrentMessages());

        $this->helper->addMessage('bar-info', FlashMessenger::NAMESPACE_INFO);
        $this->assertTrue($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_INFO));
    }

    public function testAddMessagesDoesNotChangeNamespace()
    {
        $this->helper->setNamespace('foo');
        $this->helper->addMessage('bar-info', FlashMessenger::NAMESPACE_INFO);
        $this->assertEquals('foo', $this->helper->getNamespace());
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
        $this->assertTrue($this->helper->hasInfoMessages());
        $this->assertTrue($this->helper->hasMessages(FlashMessenger::NAMESPACE_INFO));
        $this->assertTrue($this->helper->hasSuccessMessages());
        $this->assertTrue($this->helper->hasMessages(FlashMessenger::NAMESPACE_SUCCESS));
        $this->assertTrue($this->helper->hasErrorMessages());
        $this->assertTrue($this->helper->hasMessages(FlashMessenger::NAMESPACE_ERROR));

        $this->helper->clearMessages();
        $this->assertFalse($this->helper->hasMessages());
        $this->assertTrue($this->helper->hasInfoMessages());
        $this->assertTrue($this->helper->hasMessages(FlashMessenger::NAMESPACE_INFO));
        $this->assertTrue($this->helper->hasSuccessMessages());
        $this->assertTrue($this->helper->hasMessages(FlashMessenger::NAMESPACE_SUCCESS));
        $this->assertTrue($this->helper->hasErrorMessages());
        $this->assertTrue($this->helper->hasMessages(FlashMessenger::NAMESPACE_ERROR));

        $this->helper->clearMessagesFromNamespace(FlashMessenger::NAMESPACE_INFO);
        $this->assertFalse($this->helper->hasInfoMessages());
        $this->assertFalse($this->helper->hasMessages(FlashMessenger::NAMESPACE_INFO));

        $this->helper->clearMessages(FlashMessenger::NAMESPACE_SUCCESS);
        $this->assertFalse($this->helper->hasSuccessMessages());
        $this->assertFalse($this->helper->hasMessages(FlashMessenger::NAMESPACE_SUCCESS));

        $this->helper->clearMessagesFromContainer();
        $this->assertFalse($this->helper->hasMessages());
        $this->assertFalse($this->helper->hasInfoMessages());
        $this->assertFalse($this->helper->hasMessages(FlashMessenger::NAMESPACE_INFO));
        $this->assertFalse($this->helper->hasSuccessMessages());
        $this->assertFalse($this->helper->hasMessages(FlashMessenger::NAMESPACE_SUCCESS));
        $this->assertFalse($this->helper->hasErrorMessages());
        $this->assertFalse($this->helper->hasMessages(FlashMessenger::NAMESPACE_ERROR));
    }

    public function testCanRetrieveMessages()
    {
        $this->seedMessages();
        $this->assertTrue($this->helper->hasMessages());
        $messages = $this->helper->getMessages();
        $this->assertEquals(2, count($messages));
        $this->assertContains('foo', $messages);
        $this->assertContains('bar', $messages);

        $messages = $this->helper->getInfoMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-info', $messages);

        $messages = $this->helper->getMessagesFromNamespace(FlashMessenger::NAMESPACE_INFO);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-info', $messages);

        $messages = $this->helper->getMessages(FlashMessenger::NAMESPACE_INFO);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-info', $messages);

        $messages = $this->helper->getSuccessMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-success', $messages);

        $messages = $this->helper->getMessagesFromNamespace(FlashMessenger::NAMESPACE_SUCCESS);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-success', $messages);

        $messages = $this->helper->getMessages(FlashMessenger::NAMESPACE_SUCCESS);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-success', $messages);

        $messages = $this->helper->getErrorMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-error', $messages);

        $messages = $this->helper->getMessagesFromNamespace(FlashMessenger::NAMESPACE_ERROR);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-error', $messages);

        $messages = $this->helper->getMessages(FlashMessenger::NAMESPACE_ERROR);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-error', $messages);
    }

    public function testCanRetrieveCurrentMessages()
    {
        $this->seedMessages();
        $messages = $this->helper->getCurrentMessages();
        $this->assertEquals(2, count($messages));
        $this->assertContains('foo', $messages);
        $this->assertContains('bar', $messages);

        $messages = $this->helper->getCurrentInfoMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-info', $messages);

        $messages = $this->helper->getCurrentMessagesFromNamespace(FlashMessenger::NAMESPACE_INFO);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-info', $messages);

        $messages = $this->helper->getCurrentMessages(FlashMessenger::NAMESPACE_INFO);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-info', $messages);

        $messages = $this->helper->getCurrentSuccessMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-success', $messages);

        $messages = $this->helper->getCurrentMessagesFromNamespace(FlashMessenger::NAMESPACE_SUCCESS);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-success', $messages);

        $messages = $this->helper->getCurrentMessages(FlashMessenger::NAMESPACE_SUCCESS);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-success', $messages);

        $messages = $this->helper->getCurrentErrorMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-error', $messages);

        $messages = $this->helper->getCurrentMessagesFromNamespace(FlashMessenger::NAMESPACE_ERROR);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-error', $messages);

        $messages = $this->helper->getCurrentMessages(FlashMessenger::NAMESPACE_ERROR);
        $this->assertEquals(1, count($messages));
        $this->assertContains('bar-error', $messages);
    }

    public function testCanClearCurrentMessages()
    {
        $this->helper->addMessage('foo');
        $this->assertTrue($this->helper->hasCurrentMessages());
        $this->helper->clearCurrentMessages();
        $this->assertFalse($this->helper->hasCurrentMessages());

        $this->seedMessages();
        $this->assertTrue($this->helper->hasCurrentMessages());
        $this->assertTrue($this->helper->hasCurrentInfoMessages());
        $this->assertTrue($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_INFO));
        $this->assertTrue($this->helper->hasCurrentSuccessMessages());
        $this->assertTrue($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_SUCCESS));
        $this->assertTrue($this->helper->hasCurrentErrorMessages());
        $this->assertTrue($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_ERROR));

        $this->helper->clearCurrentMessages();
        $this->assertFalse($this->helper->hasCurrentMessages());
        $this->assertTrue($this->helper->hasCurrentInfoMessages());
        $this->assertTrue($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_INFO));
        $this->assertTrue($this->helper->hasCurrentSuccessMessages());
        $this->assertTrue($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_SUCCESS));
        $this->assertTrue($this->helper->hasCurrentErrorMessages());
        $this->assertTrue($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_ERROR));

        $this->helper->clearCurrentMessagesFromNamespace(FlashMessenger::NAMESPACE_INFO);
        $this->assertFalse($this->helper->hasCurrentInfoMessages());
        $this->assertFalse($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_INFO));

        $this->helper->clearCurrentMessages(FlashMessenger::NAMESPACE_SUCCESS);
        $this->assertFalse($this->helper->hasCurrentSuccessMessages());
        $this->assertFalse($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_SUCCESS));

        $this->helper->clearCurrentMessagesFromContainer();
        $this->assertFalse($this->helper->hasCurrentMessages());
        $this->assertFalse($this->helper->hasCurrentInfoMessages());
        $this->assertFalse($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_INFO));
        $this->assertFalse($this->helper->hasCurrentSuccessMessages());
        $this->assertFalse($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_SUCCESS));
        $this->assertFalse($this->helper->hasCurrentErrorMessages());
        $this->assertFalse($this->helper->hasCurrentMessages(FlashMessenger::NAMESPACE_ERROR));
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

    public function testAddMessageWithLoops()
    {
        $helper  = new FlashMessenger();
        $helper->addMessage('foo');
        $helper->addMessage('bar', null, 2);
        $helper->addMessage('baz', null, 5);
        $this->assertEquals('3', count($helper->getCurrentMessages()));
        $helper->clearCurrentMessages();
        $this->assertEquals('0', count($helper->getCurrentMessages()));
    }
}
