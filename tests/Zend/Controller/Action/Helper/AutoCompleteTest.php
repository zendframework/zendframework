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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Controller_Action_Helper_AutoCompleteTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_AutoCompleteTest::main");
}





/**
 * Test class for Zend_Controller_Action_Helper_AutoComplete.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_AutoCompleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_AutoCompleteTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Zend_Controller_Action_Helper_AutoCompleteTest_LayoutOverride::resetMvcInstance();
        Zend_Controller_Action_HelperBroker::resetHelpers();
        Zend_Controller_Action_HelperBroker::setPluginLoader(null);

        $this->request = new Zend_Controller_Request_Http();
        $this->response = new Zend_Controller_Response_Cli();
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setRequest($this->request)->setResponse($this->response);

        $this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->layout = Zend_Layout::startMvc();
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
        $dojo = new Zend_Controller_Action_Helper_AutoCompleteDojo();
        $this->assertTrue($dojo instanceof Zend_Controller_Action_Helper_AutoComplete_Abstract);

        $scriptaculous = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $this->assertTrue($scriptaculous instanceof Zend_Controller_Action_Helper_AutoComplete_Abstract);
    }

    public function testEncodeJsonProxiesToJsonActionHelper()
    {
        $dojo    = new Zend_Controller_Action_Helper_AutoCompleteDojo();
        $data    = array('foo', 'bar', 'baz');
        $encoded = $dojo->prepareAutoCompletion($data);
        $decoded = Zend_Json::decode($encoded);
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
        $dojo = new Zend_Controller_Action_Helper_AutoCompleteDojo();
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data, false);
        $decoded = Zend_Json::decode($encoded);
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
        $dojo = new Zend_Controller_Action_Helper_AutoCompleteDojo();
        $dojo->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data);
        $decoded = Zend_Json::decode($encoded);
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
        $dojo = new Zend_Controller_Action_Helper_AutoCompleteDojo();
        $dojo->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data);
        $this->assertFalse($this->layout->isEnabled());
        $this->assertTrue($this->viewRenderer->getNoRender());
    }

    public function testDojoHelperCanEnableLayoutsAndViewRenderer()
    {
        $dojo = new Zend_Controller_Action_Helper_AutoCompleteDojo();
        $dojo->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $dojo->direct($data, false, true);
        $this->assertTrue($this->layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
    }

    public function testScriptaculousHelperThrowsExceptionOnInvalidDataFormat()
    {
        $scriptaculous = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();

        $data = new stdClass;
        $data->foo = 'bar';
        $data->bar = 'baz';
        try {
            $encoded = $scriptaculous->encodeJson($data);
            $this->fail('Objects should be considered invalid');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertContains('Invalid data', $e->getMessage());
        }
    }

    public function testScriptaculousHelperCreatesHtmlMarkup()
    {
        $scriptaculous = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
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
        $scriptaculous = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $scriptaculous->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $scriptaculous->direct($data);
        $body = $this->response->getBody();
        $this->assertSame($encoded, $body);
    }

    public function testScriptaculousHelperDisablesLayoutsAndViewRendererByDefault()
    {
        $scriptaculous = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $scriptaculous->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $scriptaculous->direct($data);
        $this->assertFalse($this->layout->isEnabled());
        $this->assertTrue($this->viewRenderer->getNoRender());
    }

    public function testScriptaculousHelperCanEnableLayoutsAndViewRenderer()
    {
        $scriptaculous = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $scriptaculous->suppressExit = true;
        $data = array('foo', 'bar', 'baz');
        $encoded = $scriptaculous->direct($data, false, true);
        $this->assertTrue($this->layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
    }
}

class Zend_Controller_Action_Helper_AutoCompleteTest_LayoutOverride extends Zend_Layout
{
    public static function resetMvcInstance()
    {
        self::$_mvcInstance = null;
    }
}

// Call Zend_Controller_Action_Helper_AutoCompleteTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_AutoCompleteTest::main") {
    Zend_Controller_Action_Helper_AutoCompleteTest::main();
}
