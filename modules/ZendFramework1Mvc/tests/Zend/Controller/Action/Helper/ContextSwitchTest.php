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
namespace ZendTest\Controller\Action\Helper;

use Zend\Layout,
    Zend\Controller\Action\Helper,
    Zend\Controller\Action,
    Zend\Controller,
    Zend\Config,
    Zend\Json\Json;

/**
 * Test class for Zend_Controller_Action_Helper_ContextSwitch.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class ContextSwitchTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        \Zend\Layout\Layout::resetMvcInstance();

        $this->front = Controller\Front::getInstance();
        $this->front->resetInstance();
        $this->front->addModuleDirectory(__DIR__ . '/../../_files/modules');
        $this->broker = $this->front->getHelperBroker();

        $this->layout = Layout\Layout::startMvc();

        $this->helper = new Helper\ContextSwitch();
        $this->broker->register('contextswitch', $this->helper);

        $this->request = new \Zend\Controller\Request\Http();
        $this->response = new \Zend\Controller\Response\Cli();

        $this->front->setRequest($this->request)
                    ->setResponse($this->response)
                    ->addControllerDirectory(__DIR__);

        $this->view = new \Zend\View\PhpRenderer();
        $this->viewRenderer = $this->broker->load('viewRenderer');
        $this->viewRenderer->setView($this->view);

        $this->controller = new ContextSwitchTestController(
            $this->request,
            $this->response,
            array()
        );
        $this->controller->setHelperBroker($this->broker);
        $this->controller->setupContexts();
        $this->helper->setActionController($this->controller);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testDirectReturnsObjectInstance()
    {
        $helper = $this->helper->direct();
        $this->assertSame($this->helper, $helper);
    }

    public function testSetSuffixModifiesContextSuffix()
    {
        $this->helper->setSuffix('xml', 'foobar');
        $this->assertContains('foobar', $this->helper->getSuffix('xml'));
    }

    public function testSetSuffixPrependsToViewRendererSuffixByDefault()
    {
        $this->helper->setSuffix('xml', 'foobar');
        $expected = 'foobar.' . $this->viewRenderer->getViewSuffix();
        $this->assertContains($expected, $this->helper->getSuffix('xml'));
    }

    public function testCanSetSuffixWithoutViewRendererSuffix()
    {
        $this->helper->setSuffix('xml', 'foobar', false);
        $expected = 'foobar';
        $this->assertContains($expected, $this->helper->getSuffix('xml'));
    }

    public function testSuffixAccessorsThrowExceptionOnInvalidContextType()
    {
        try {
            $this->helper->setSuffix('foobar', 'foobar');
            $this->fail('setSuffix() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('Cannot set suffix', $e->getMessage());
        }

        try {
            $this->helper->getSuffix('foobar');
            $this->fail('getSuffix() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('Cannot retrieve suffix', $e->getMessage());
        }
    }

    public function testCanAddAdditionalHeadersPerContext()
    {
        $this->helper->addHeader('xml', 'X-Foo', 'Bar');
        $headers = $this->helper->getHeaders('xml');
        $this->assertTrue(isset($headers['Content-Type']));
        $this->assertEquals('application/xml', $headers['Content-Type']);
        $this->assertTrue(isset($headers['X-Foo']));
        $this->assertEquals('Bar', $headers['X-Foo']);
    }

    public function testCanAddMultipleHeadersPerContextSimultaneously()
    {
        $this->helper->addHeaders('xml', array(
            'X-Foo' => 'Bar',
            'X-Bar' => 'Baz'
        ));
        $headers = $this->helper->getHeaders('xml');
        $this->assertTrue(isset($headers['Content-Type']));
        $this->assertEquals('application/xml', $headers['Content-Type']);
        $this->assertTrue(isset($headers['X-Foo']));
        $this->assertEquals('Bar', $headers['X-Foo']);
        $this->assertTrue(isset($headers['X-Bar']));
        $this->assertEquals('Baz', $headers['X-Bar']);
    }

    public function testAddHeaderThrowsExceptionWhenReferencingExistingHeader()
    {
        try {
            $this->helper->addHeader('xml', 'Content-Type', 'application/xml');
            $this->fail('addHeader() should raise exception for existing headers');
        } catch (Controller\Exception $e) {
            $this->assertContains('already exists', $e->getMessage());
        }
    }

    public function testSetHeaderOverwritesHeaderExistingHeader()
    {
        $this->helper->setHeader('xml', 'Content-Type', 'application/foo-xml');
        $this->assertEquals('application/foo-xml', $this->helper->getHeader('xml', 'Content-Type'));
    }

    public function testSetHeadersOverwritesHeaders()
    {
        $headers = array(
            'X-Foo' => 'Bar',
            'X-Bar' => 'Baz'
        );
        $this->helper->setHeaders('xml', $headers);
        $this->assertEquals($headers, $this->helper->getHeaders('xml'));
    }

    public function testCanRemoveSingleHeaders()
    {
        $this->helper->addHeader('xml', 'X-Foo', 'Bar');
        $this->assertEquals('Bar', $this->helper->getHeader('xml', 'X-Foo'));
        $this->helper->removeHeader('xml', 'X-Foo');
        $this->assertNull($this->helper->getHeader('xml', 'X-Foo'));
    }

    public function testCanClearAllHeaders()
    {
        $this->helper->addHeader('xml', 'X-Foo', 'Bar');
        $expected = array('Content-Type' => 'application/xml', 'X-Foo' => 'Bar');
        $this->assertEquals($expected, $this->helper->getHeaders('xml'));
        $this->helper->clearHeaders('xml');
        $this->assertEquals(array(), $this->helper->getHeaders('xml'));
    }

    public function testHeaderAccessorsThrowExceptionOnInvalidContextType()
    {
        try {
            $this->helper->addHeader('foobar', 'foobar', 'baz');
            $this->fail('addHeader() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        try {
            $this->helper->setHeader('foobar', 'foobar', 'baz');
            $this->fail('setHeader() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        try {
            $this->helper->getHeader('foobar', 'Content-Type');
            $this->fail('getHeader() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        try {
            $this->helper->getHeaders('foobar');
            $this->fail('getHeaders() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        try {
            $this->helper->addHeaders('foobar', array('X-Foo' => 'Bar'));
            $this->fail('addHeaders() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        try {
            $this->helper->setHeaders('foobar', array('X-Foo' => 'Bar'));
            $this->fail('setHeaders() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        try {
            $this->helper->removeHeader('foobar', 'X-Foo');
            $this->fail('removeHeader() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        try {
            $this->helper->clearHeaders('foobar');
            $this->fail('clearHeaders() should throw exception with invalid context type');
        } catch (Action\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
    }

    public function testCanSetCallbackByContextAndTrigger()
    {
        $this->helper->setCallback('xml', 'init', 'htmlentities');
        $this->assertEquals('htmlentities', $this->helper->getCallback('xml', 'init'));

        $this->helper->setCallback('xml', 'post', array('Zend_Controller_Action_Helper_ContextSwitchTest', 'main'));
        $this->assertSame(array('Zend_Controller_Action_Helper_ContextSwitchTest', 'main'), $this->helper->getCallback('xml', 'post'));
    }

    public function testCanSetAllCallbacksByContext()
    {
        $callbacks = array(
            'init' => 'htmlentities',
            'post' => array('Zend_Loader', 'registerAutoload')
        );
        $this->helper->setCallbacks('xml', $callbacks);
        $returned = $this->helper->getCallbacks('xml');
        $this->assertSame(array_values($callbacks), array_values($returned));
    }

    public function testCanRemoveCallbackByContextAndTrigger()
    {
        $this->testCanSetCallbackByContextAndTrigger();
        $this->helper->removeCallback('xml', 'init');
        $this->assertNull($this->helper->getCallback('xml', 'init'));
    }

    public function testCanClearAllCallbacksByContext()
    {
        $this->testCanSetCallbackByContextAndTrigger();
        $this->helper->clearCallbacks('xml');
        $this->assertSame(array(), $this->helper->getCallbacks('xml'));
    }

    public function testCanAddContext()
    {
        $this->helper->addContext('foobar', array(
            'suffix'  => 'foo.bar',
            'headers' => array('Content-Type' => 'application/x-foobar', 'X-Foo' => 'Bar'),
        ));
        $context = $this->helper->getContext('foobar');
        $this->assertNotNull($context);
        $this->assertTrue(is_array($context));
        $this->assertTrue(isset($context['suffix']));
        $this->assertTrue(isset($context['headers']));
        $this->assertTrue(isset($context['callbacks']));

        $this->assertContains('foo.bar', $context['suffix']);
        $this->assertEquals('application/x-foobar', $context['headers']['Content-Type']);
        $this->assertEquals('Bar', $context['headers']['X-Foo']);
    }

    public function testAddContextThrowsExceptionIfContextAlreadyExists()
    {
        try {
            $this->helper->addContext('xml', array());
            $this->fail('Shold not be able to add context if already exists');
        } catch (Controller\Exception $e) {
            $this->assertContains('exists', $e->getMessage());
        }
    }

    public function testSetContextOverwritesExistingContext()
    {
        $this->helper->setContext('xml', array());
        $this->assertNull($this->helper->getHeader('xml', 'Content-Type'));
        $this->assertEquals($this->viewRenderer->getViewSuffix(), $this->helper->getSuffix('xml'));
    }

    public function testCanAddMultipleContextsAtOnce()
    {
        $this->helper->addContexts(array(
            'foobar' => array(
                'suffix'  => 'foo.bar',
                'headers' => array('Content-Type' => 'application/x-foobar', 'X-Foo' => 'Bar'),
            ),
            'barbaz' => array(
                'suffix'  => 'bar.baz',
                'headers' => array('Content-Type' => 'application/x-barbaz', 'X-Bar' => 'Baz'),
            )
        ));
        $this->assertTrue($this->helper->hasContext('foobar'));
        $this->assertTrue($this->helper->hasContext('barbaz'));
    }

    public function testCanOverwriteManyContextsAtOnce()
    {
        $this->helper->setContexts(array(
            'xml'    => array(
                'suffix'    => array('suffix' => 'xml', 'prependViewRendererSuffix' => false),
                'headers'   => array('Content-Type' => 'application/xml'),
                'callbacks' => array('TRIGGER_INIT' => 'foobar')
            ),
            'foobar' => array(
                'suffix'  => 'foo.bar',
                'headers' => array('Content-Type' => 'application/x-foobar', 'X-Foo' => 'Bar'),
            ),
            'barbaz' => array(
                'suffix'  => 'bar.baz',
                'headers' => array('Content-Type' => 'application/x-barbaz', 'X-Bar' => 'Baz'),
            )
        ));
        $this->assertTrue($this->helper->hasContext('xml'));
        $this->assertFalse($this->helper->hasContext('json'));
        $this->assertTrue($this->helper->hasContext('foobar'));
        $this->assertTrue($this->helper->hasContext('barbaz'));
        $this->assertEquals('xml', $this->helper->getSuffix('xml'));
        $this->assertNotEquals('foo.bar', $this->helper->getSuffix('foobar'));
        $this->assertContains('foo.bar', $this->helper->getSuffix('foobar'));
        $this->assertNotEquals('bar.baz', $this->helper->getSuffix('barbaz'));
        $this->assertContains('bar.baz', $this->helper->getSuffix('barbaz'));
    }

    public function testCanRemoveSingleContext()
    {
        $this->assertTrue($this->helper->hasContext('xml'));
        $this->helper->removeContext('xml');
        $this->assertFalse($this->helper->hasContext('xml'));
    }

    public function testCanClearAllContexts()
    {
        $this->assertTrue($this->helper->hasContext('xml'));
        $this->assertTrue($this->helper->hasContext('json'));
        $contexts = $this->helper->getContexts();
        $this->helper->clearContexts();
        $received = $this->helper->getContexts();
        $this->assertNotEquals($contexts, $received);
        $this->assertTrue(empty($received));
    }

    public function testDefaultContextParam()
    {
        $this->assertEquals('format', $this->helper->getContextParam());
    }

    public function testCanSetContextParam()
    {
        $this->helper->setContextParam('foobar');
        $this->assertEquals('foobar', $this->helper->getContextParam());
    }

    public function testDefaultContext()
    {
        $this->assertEquals('xml', $this->helper->getDefaultContext());
    }

    public function testCanSetDefaultContext()
    {
        $this->helper->setDefaultContext('json');
        $this->assertEquals('json', $this->helper->getDefaultContext());
    }

    public function testSetDefaultContextThrowsExceptionIfContextDoesNotExist()
    {
        try {
            $this->helper->setDefaultContext('foobar');
            $this->fail('setDefaultContext() should raise exception if context does not exist');
        } catch (Action\Exception $e) {
            $this->assertContains('Cannot set default context', $e->getMessage());
        }
    }

    public function testContextSwitchDisablesLayoutsByDefault()
    {
        $this->assertTrue($this->helper->getAutoDisableLayout());
    }

    public function testCanChooseWhetherLayoutsAreDisabled()
    {
        $this->helper->setAutoDisableLayout(false);
        $this->assertFalse($this->helper->getAutoDisableLayout());
        $this->helper->setAutoDisableLayout(true);
        $this->assertTrue($this->helper->getAutoDisableLayout());
    }

    public function checkNothingIsDone()
    {
        $this->assertEquals('phtml', $this->viewRenderer->getViewSuffix());
        $headers = $this->response->getHeaders();
        $this->assertTrue(empty($headers));
    }

    public function testInitContextDoesNothingIfNoContextsSet()
    {
        unset($this->controller->contexts);
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextThrowsExceptionIfControllerContextsIsInvalid()
    {
        $this->controller->contexts = 'foo';
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        try {
            $this->helper->initContext();
            $this->fail('Invalid contexts array should cause failure');
        } catch (Controller\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
        $this->checkNothingIsDone();
    }

    public function testInitContextDoesNothingIfActionHasNoContexts()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('baz');
        $this->helper->initContext();
        $this->checkNothingIsDone();

        $this->request->setParam('format', 'json')
                      ->setActionName('baz');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextDoesNothingIfActionDoesNotHaveContext()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextUsesBooleanTrueActionValueToAssumeAllContexts()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('all');
        $this->helper->initContext();
        $this->assertEquals('json', $this->helper->getCurrentContext());
        $this->assertContains('json', $this->viewRenderer->getViewSuffix());

        $this->request->setParam('format', 'xml')
                      ->setActionName('all');
        $this->helper->initContext();
        $this->assertEquals('xml', $this->helper->getCurrentContext());
        $this->assertContains('xml', $this->viewRenderer->getViewSuffix());
    }

    public function testInitContextDoesNothingIfActionDoesNotHaveContextAndPassedFormatInvalid()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('foo');
        $this->helper->initContext('bogus');
        $this->checkNothingIsDone();
    }

    public function testInitContextSetsViewRendererViewSuffix()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertContains('xml', $this->viewRenderer->getViewSuffix());
    }

    public function testInitContextSetsAppropriateResponseHeader()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/xml', $value);
    }

    public function testInitContextUsesPassedFormatWhenContextParamPresent()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext('json');

        $this->assertContains('json', $this->viewRenderer->getViewSuffix());

        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/json', $value);
    }

    public function testInitContextUsesPassedFormatWhenNoContextParamNotPresent()
    {
        $this->request->setActionName('foo');
        $this->helper->initContext('xml');

        $this->assertContains('xml', $this->viewRenderer->getViewSuffix());

        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/xml', $value);
    }

    public function testInitContextDisablesLayoutByDefault()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();

        $this->assertFalse($this->layout->isEnabled());
    }

    public function testInitContextDoesNotDisableLayoutIfDisableLayoutDisabled()
    {
        $this->helper->setAutoDisableLayout(false);
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();

        $this->assertTrue($this->layout->isEnabled());
    }

    public function testGetCurrentContextInitiallyNull()
    {
        $this->assertNull($this->helper->getCurrentContext());
    }

    public function testGetCurrentContextReturnsContextAfterInitContextIsSuccessful()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();

        $this->assertEquals('xml', $this->helper->getCurrentContext());
    }

    public function testGetCurrentContextResetToNullWhenSubsequentInitContextFails()
    {
        $this->assertNull($this->helper->getCurrentContext());

        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertEquals('xml', $this->helper->getCurrentContext());

        $this->request->setParam('format', 'foo')
                      ->setActionName('bogus');
        $this->helper->initContext();
        $this->assertNull($this->helper->getCurrentContext());
    }

    public function testGetCurrentContextChangesAfterSubsequentInitContextCalls()
    {
        $this->assertNull($this->helper->getCurrentContext());

        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertEquals('xml', $this->helper->getCurrentContext());

        $this->request->setParam('format', 'json')
                      ->setActionName('bar');
        $this->helper->initContext();
        $this->assertEquals('json', $this->helper->getCurrentContext());
    }

    public function testJsonContextShouldEncodeViewVariablesByDefaultAndNotRequireRenderingView()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('bar')
                      ->setDispatched(true);
        $this->controller->dispatch('barAction');

        $headers = $this->response->getHeaders();
        $found   = false;
        foreach ($headers as $header) {
            if ($header['name'] == 'Content-Type') {
                if ($header['value'] == 'application/json') {
                    $found = true;
                }
                break;
            }
        }
        $this->assertTrue($found, 'JSON content type header not found');

        $body = $this->response->getBody();
        $result = Json::decode($body, Json::TYPE_ARRAY);
        $this->assertTrue(is_array($result), var_export($body, 1));
        $this->assertTrue(isset($result['foo']), var_export($result, 1));
        $this->assertTrue(isset($result['bar']));
        $this->assertEquals('bar', $result['foo']);
        $this->assertEquals('baz', $result['bar']);
    }

    public function testAutoJsonSerializationMayBeDisabled()
    {
        $this->request->setParam('format', 'json')
                      ->setActionName('bar')
                      ->setDispatched(true);
        $this->helper->setAutoJsonSerialization(false);
        $this->controller->dispatch('barAction');


        $headers = $this->response->getHeaders();
        $found   = false;
        foreach ($headers as $header) {
            if ($header['name'] == 'Content-Type') {
                if ($header['value'] == 'application/json') {
                    $found = true;
                }
                break;
            }
        }
        $this->assertTrue($found, 'JSON content type header not found');

        $body = $this->response->getBody();
        $this->assertTrue(empty($body), $body);
    }

    public function testCanAddOneOrMoreActionContexts()
    {
        $this->assertFalse($this->helper->hasActionContext('foo', 'json'));
        $this->helper->addActionContext('foo', 'json');
        $this->assertTrue($this->helper->hasActionContext('foo', 'json'));

        $this->assertFalse($this->helper->hasActionContext('baz', 'xml'));
        $this->assertFalse($this->helper->hasActionContext('baz', 'json'), var_export($this->controller->contexts, 1));
        $this->helper->addActionContext('baz', array('xml', 'json'));
        $this->assertTrue($this->helper->hasActionContext('baz', 'xml'));
        $this->assertTrue($this->helper->hasActionContext('baz', 'json'));
    }

    public function testCanOverwriteAnActionContext()
    {
        $this->assertTrue($this->helper->hasActionContext('foo', 'xml'));
        $this->helper->setActionContext('foo', 'json');
        $this->assertFalse($this->helper->hasActionContext('foo', 'xml'));
        $this->assertTrue($this->helper->hasActionContext('foo', 'json'));
        $this->helper->setActionContext('foo', array('xml', 'json'));
        $this->assertTrue($this->helper->hasActionContext('foo', 'json'));
        $this->assertTrue($this->helper->hasActionContext('foo', 'xml'));
    }

    public function testCanAddContextsForMultipleActions()
    {
        $this->assertFalse($this->helper->hasActionContext('foo', 'json'));
        $this->assertFalse($this->helper->hasActionContext('baz', 'json'));
        $this->assertFalse($this->helper->hasActionContext('baz', 'xml'));
        $this->helper->addActionContexts(array(
            'foo' => 'json',
            'baz' => array('json', 'xml'),
        ));
        $this->assertTrue($this->helper->hasActionContext('foo', 'json'));
        $this->assertTrue($this->helper->hasActionContext('baz', 'json'));
        $this->assertTrue($this->helper->hasActionContext('baz', 'xml'));
    }

    public function testCanOverwriteContextsForMultipleActions()
    {
        $this->assertTrue($this->helper->hasActionContext('foo', 'xml'));
        $this->assertTrue($this->helper->hasActionContext('bar', 'json'));
        $this->assertTrue($this->helper->hasActionContext('bar', 'xml'));
        $this->helper->setActionContexts(array(
            'foo' => 'json',
            'bar' => 'xml'
        ));
        $this->assertFalse($this->helper->hasActionContext('foo', 'xml'));
        $this->assertTrue($this->helper->hasActionContext('foo', 'json'));
        $this->assertFalse($this->helper->hasActionContext('bar', 'json'));
        $this->assertTrue($this->helper->hasActionContext('bar', 'xml'));
    }

    public function testCanRemoveOneOrMoreActionContexts()
    {
        $this->assertTrue($this->helper->hasActionContext('bar', 'json'));
        $this->assertTrue($this->helper->hasActionContext('bar', 'xml'));
        $this->helper->removeActionContext('bar', 'xml');
        $this->assertTrue($this->helper->hasActionContext('bar', 'json'));
        $this->assertFalse($this->helper->hasActionContext('bar', 'xml'));
    }

    public function testCanClearAllContextsForASingleAction()
    {
        $this->assertTrue($this->helper->hasActionContext('bar', 'json'));
        $this->assertTrue($this->helper->hasActionContext('bar', 'xml'));
        $this->helper->clearActionContexts('bar');
        $this->assertFalse($this->helper->hasActionContext('bar', 'json'));
        $this->assertFalse($this->helper->hasActionContext('bar', 'xml'));
    }

    public function testCanClearAllActionContexts()
    {
        $this->helper->clearActionContexts();
        $contexts = $this->helper->getActionContexts();
        $this->assertTrue(empty($contexts));
    }

    public function getOptions()
    {
        $options = array(
            'contexts' => array('ajax' => array('suffix' => 'ajax', 'headers' => array('Content-Type' => 'text/x-html')), 'json' => array('suffix' => 'json', 'headers' => array('Content-Type' => 'application/json'), 'callbacks' => array('init' => 'initJsonCallback', 'post' => 'postJsonCallback'))),
            'autoJsonSerialization' => false,
            'suffix' => array('json' => array('suffix' => 'js', 'prependViewRendererSuffix' => false)),
            'headers' => array('json' => array('Content-Type' => 'text/js')),
            'callbacks' => array('json' => array('init' => 'htmlentities')),
            'contextParam' => 'foobar',
            'defaultContext' => 'json',
            'autoDisableLayout' => false,
        );
        return $options;
    }

    public function checkOptionsAreSet()
    {
        $this->assertFalse($this->helper->getAutoJsonSerialization());
        $this->assertEquals('js', $this->helper->getSuffix('json'));
        $this->assertEquals('text/js', $this->helper->getHeader('json', 'Content-Type'));
        $this->assertEquals('htmlentities', $this->helper->getCallback('json', 'init'));
        $this->assertEquals('foobar', $this->helper->getContextParam());
        $this->assertEquals('json', $this->helper->getDefaultContext());
        $this->assertFalse($this->helper->getAutoDisableLayout());
        $this->assertTrue($this->helper->hasContext('ajax'));
    }

    public function testCanSetOptionsViaArray()
    {
        $this->helper->setOptions($this->getOptions());
        $this->checkOptionsAreSet();
    }

    public function testCanSetOptionsViaConfig()
    {
        $config = new Config\Config($this->getOptions());
        $this->helper->setConfig($config);
        $this->checkOptionsAreSet();
    }

    public function testOptionsPassedToConstructorShouldSetInstanceState()
    {
        $this->helper = new Helper\ContextSwitch($this->getOptions());
        $this->checkOptionsAreSet();
    }

    public function testConfigPassedToConstructorShouldSetInstanceState()
    {
        $config = new Config\Config($this->getOptions());
        $this->helper = new Helper\ContextSwitch($config);
        $this->checkOptionsAreSet();
    }

    /**
     * @group ZF-3279
     */
    public function testPostJsonContextDoesntThrowExceptionWhenGetVarsMethodsExists()
    {
        try {
            $this->helper->setAutoJsonSerialization(true);
            $this->helper->postJsonContext();
        } catch(Action\Exception $zcae) {
            $this->fail('Exception should be throw when view does not implement getVars() method');
        }
    }

    /**
     * @group ZF-3279
     */
    public function testPostJsonContextThrowsExceptionWhenVarsMethodsDoesntExist()
    {
        $view = new CustomView();
        $this->viewRenderer->setView($view);

        try {
            $this->helper->setAutoJsonSerialization(true);
            $this->helper->postJsonContext();
            $this->fail('Exception should be throw when view does not implement getVars() method');
        } catch(Action\Exception $zcae) {
        }
    }

    /**
     * @group ZF-4866
     */
    public function testForwardingShouldNotUseContextSuffixIfNewActionDoesNotDetectValidContext()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->setActionContext('bar', 'json');
        $this->helper->initContext();
        $this->assertEquals('xml', $this->helper->getCurrentContext());
        $this->request->setActionName('bar');
        $this->helper->init();
        $this->helper->initContext();
        $suffix = $this->viewRenderer->getViewSuffix();
        $this->assertNotContains('xml', $suffix, $suffix);
    }

    /**
     * @group ZF-4866
     */
    public function testForwardingShouldNotPrependMultipleViewSuffixesForCustomContexts()
    {
        $this->helper->addContext('foo', array('suffix' => 'foo'));
        $this->helper->setActionContext('foo', 'foo');
        $this->helper->setActionContext('bar', 'foo');
        $this->request->setParam('format', 'foo')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertEquals('foo', $this->helper->getCurrentContext());
        $suffix = $this->viewRenderer->getViewSuffix();
        $this->assertContains('foo', $suffix, $suffix);

        $this->request->setActionName('bar');
        $this->helper->init();
        $this->helper->initContext();
        $this->assertEquals('foo', $this->helper->getCurrentContext());
        $suffix = $this->viewRenderer->getViewSuffix();
        $this->assertContains('foo', $suffix, $suffix);
        $this->assertNotContains('foo.foo', $suffix, $suffix);
    }
}

class ContextSwitchTestController extends Action
{
    public $contextSwitch;

    /*
    public $contexts = array(
        'foo' => array('xml'),          // only XML context
        'bar' => array('xml', 'json'),  // only XML and JSON contexts
        'baz' => array(),               // no contexts
        'all' => true,                  // all contexts
    );
     */

    public function setupContexts()
    {
        $this->broker('contextSwitch')->setActionContexts(array(
            'foo' => 'xml',
            'bar' => array('xml', 'json'),
            'all' => true
        ));
    }

    public function postDispatch()
    {
        $this->broker('viewRenderer')->setNoRender();
    }

    public function barAction()
    {
        $this->broker('contextSwitch')->initContext();
        $this->view->vars()->assign(array(
            'foo' => 'bar',
            'bar' => 'baz',
        ));
    }
}


class CustomView implements \Zend\View\Renderer
{
    public function getEngine()
    {}

    public function render($name)
    {}
}
