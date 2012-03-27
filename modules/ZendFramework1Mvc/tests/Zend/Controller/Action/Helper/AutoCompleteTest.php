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

use Zend\Controller\Action\Exception as ActionException,
    Zend\Controller\Action\Helper\AbstractAutoComplete,
    Zend\Controller\Action\Helper\AutoCompleteDojo,
    Zend\Controller\Action\Helper\AutoCompleteScriptaculous,
    Zend\Controller\Front as FrontController,
    Zend\Controller\Request\Http as HTTPRequest,
    Zend\Controller\Response\Cli as CLIResponse,
    Zend\Layout\Layout,
    Zend\Json\Json;


/**
 * Test class for Zend_Controller_Action_Helper_AutoComplete.
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
class AutoCompleteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Layout::resetMvcInstance();

        $this->request = new HTTPRequest();
        $this->response = new CLIResponse();
        $this->front = FrontController::getInstance();
        $this->front->resetInstance();
        $this->front->setRequest($this->request)->setResponse($this->response);
        $this->broker = $this->front->getHelperBroker();

        $this->viewRenderer = $this->broker->load('viewRenderer');
        $this->layout = Layout::startMvc();
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

    public function testConcreteImplementationsDeriveFromAutoCompleteBaseClass()
    {
        $dojo = new AutoCompleteDojo();
        $this->assertTrue($dojo instanceof AbstractAutoComplete);

        $scriptaculous = new AutoCompleteScriptaculous();
        $this->assertTrue($scriptaculous instanceof AbstractAutoComplete);
    }

    public function testEncodeJsonProxiesToJsonActionHelper()
    {
        $dojo    = new AutoCompleteDojo();
        $dojo->setBroker($this->broker);
        $data    = array('foo', 'bar', 'baz');
        $encoded = $dojo->prepareAutoCompletion($data);
        $decoded = Json::decode($encoded, Json::TYPE_ARRAY);
        $test    = array();
        foreach ($decoded['items'] as $item) {
            $test[] = $item['name'];
        }
        $this->assertSame($data, $test);
        $this->assertFalse($this->layout->isEnabled());
        $headers = $this->response->getHeaders();
        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                if ('application/json' == $header['value']) {
                    $found = true;
                }
                break;
            }
        }
        $this->assertTrue($found, "JSON content-type header not found");
    }

    public function testDojoHelperEncodesToJson()
    {
        $dojo = new AutoCompleteDojo();
        $dojo->setBroker($this->broker);
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data, false);
        $decoded = Json::decode($encoded, Json::TYPE_ARRAY);
        $this->assertContains('items', array_keys($decoded));
        $this->assertContains('identifier', array_keys($decoded));
        $this->assertEquals('name', $decoded['identifier']);

        $test = array();
        foreach ($decoded['items'] as $item) {
            $test[] = $item['label'];
        }
        $this->assertEquals($data, $test);
    }

    public function testDojoHelperSendsResponseByDefault()
    {
        $dojo = new AutoCompleteDojo();
        $dojo->setBroker($this->broker);
        $dojo->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data);
        $decoded = Json::decode($encoded, Json::TYPE_ARRAY);
        $test    = array();
        foreach ($decoded['items'] as $item) {
            $test[] = $item['name'];
        }
        $this->assertSame($data, $test);
        $body = $this->response->getBody();
        $this->assertSame($encoded, $body);
    }

    public function testDojoHelperDisablesLayoutsAndViewRendererByDefault()
    {
        $dojo = new AutoCompleteDojo();
        $dojo->setBroker($this->broker);
        $dojo->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data);
        $this->assertFalse($this->layout->isEnabled());
        $this->assertTrue($this->viewRenderer->getNoRender());
    }

    public function testDojoHelperCanEnableLayoutsAndViewRenderer()
    {
        $dojo = new AutoCompleteDojo();
        $dojo->setBroker($this->broker);
        $dojo->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data, false, true);
        $this->assertTrue($this->layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
    }
    /**
     * @group   ZF-9126
     */
    public function testDojoHelperEncodesUnicodeChars()
    {
        $dojo = new AutoCompleteDojo();
        $dojo->setBroker($this->broker);
        $dojo->suppressExit = true;
        $data = array ('garçon', 'schließen', 'Helgi Þormar Þorbjörnsson');
        $encoded = $dojo->direct($data);
        $body = $this->response->getBody();
        $decoded = Json::decode($encoded, Json::TYPE_ARRAY);
        $test = array ();
        foreach ($decoded['items'] as $item) {
            $test[] = $item['name'];
        }
        $this->assertSame($data, $test);
        $this->assertSame($encoded, $body);
    }

    public function testScriptaculousHelperThrowsExceptionOnInvalidDataFormat()
    {
        $scriptaculous = new AutoCompleteScriptaculous();
        $scriptaculous->setBroker($this->broker);

        $data = new \stdClass;
        $data->foo = 'bar';
        $data->bar = 'baz';
        try {
            $encoded = $scriptaculous->encodeJson($data);
            $this->fail('Objects should be considered invalid');
        } catch (ActionException $e) {
            $this->assertContains('Invalid data', $e->getMessage());
        }
    }

    public function testScriptaculousHelperCreatesHtmlMarkup()
    {
        $scriptaculous = new AutoCompleteScriptaculous();
        $scriptaculous->setBroker($this->broker);
        $scriptaculous->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $formatted = $scriptaculous->direct($data);
        $this->assertContains('<ul>', $formatted);
        foreach ($data as $value) {
            $this->assertContains('<li>' . $value . '</li>', $formatted);
        }
        $this->assertContains('</ul>', $formatted);
    }

    public function testScriptaculousHelperSendsResponseByDefault()
    {
        $scriptaculous = new AutoCompleteScriptaculous();
        $scriptaculous->setBroker($this->broker);
        $scriptaculous->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $scriptaculous->direct($data);
        $body = $this->response->getBody();
        $this->assertSame($encoded, $body);
    }

    public function testScriptaculousHelperDisablesLayoutsAndViewRendererByDefault()
    {
        $scriptaculous = new AutoCompleteScriptaculous();
        $scriptaculous->setBroker($this->broker);
        $scriptaculous->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $scriptaculous->direct($data);
        $this->assertFalse($this->layout->isEnabled());
        $this->assertTrue($this->viewRenderer->getNoRender());
    }

    public function testScriptaculousHelperCanEnableLayoutsAndViewRenderer()
    {
        $scriptaculous = new AutoCompleteScriptaculous();
        $scriptaculous->setBroker($this->broker);
        $scriptaculous->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $scriptaculous->direct($data, false, true);
        $this->assertTrue($this->layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
    }
}

