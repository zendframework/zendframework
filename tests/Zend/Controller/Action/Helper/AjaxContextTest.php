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

// Call Zend_Controller_Action_Helper_AjaxContextTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_AjaxContextTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Action/Helper/AjaxContext.php';

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Layout.php';
require_once 'Zend/View.php';


/**
 * Test class for Zend_Controller_Action_Helper_AjaxContext.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_AjaxContextTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_AjaxContextTest");
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
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        }

        Zend_Controller_Action_Helper_AjaxContextTest_LayoutOverride::resetMvcInstance();
        Zend_Controller_Action_HelperBroker::resetHelpers();

        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->addModuleDirectory(dirname(__FILE__) . '/../../_files/modules');

        $this->layout = Zend_Layout::startMvc();

        $this->helper = new Zend_Controller_Action_Helper_AjaxContext();

        $this->request = new Zend_Controller_Request_Http();
        $this->response = new Zend_Controller_Response_Cli();

        $this->front->setRequest($this->request)->setResponse($this->response);
        $this->view = new Zend_VIew();
        $this->view->addHelperPath(dirname(__FILE__) . '/../../../../../library/Zend/View/Helper/');
        $this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->viewRenderer->setView($this->view);

        $this->controller = new Zend_Controller_Action_Helper_AjaxContextTestController(
            $this->request,
            $this->response,
            array()
        );
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
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        }
    }

    public function testDefaultContextsIncludesHtml()
    {
        $contexts = $this->helper->getContexts();
        $this->assertTrue(isset($contexts['html']));
        $this->assertEquals('ajax.phtml', $this->helper->getSuffix('html'));
        $header = $this->helper->getHeaders('html');
        $this->assertTrue(empty($header));
    }

    public function checkNothingIsDone()
    {
        $this->assertEquals('phtml', $this->viewRenderer->getViewSuffix());
        $headers = $this->response->getHeaders();
        $this->assertTrue(empty($headers));
    }

    public function testInitContextFailsOnNonXhrRequests()
    {
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextFailsWithNoAjaxableActions()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->request->isXmlHttpRequest());

        $this->controller->contexts = $this->controller->ajaxable;
        unset($this->controller->ajaxable);
        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->checkNothingIsDone();
    }

    public function testInitContextSwitchesContextWithXhrRequests()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->request->isXmlHttpRequest());

        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();

        $this->assertEquals('xml.phtml', $this->viewRenderer->getViewSuffix());

        $headers = $this->response->getHeaders();
        $found   = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/xml', $value);

        $this->assertFalse($this->layout->isEnabled());
    }

    public function testGetCurrentContextResetToNullWhenSubsequentInitContextFailsXhrTest()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->request->isXmlHttpRequest());

        $this->assertNull($this->helper->getCurrentContext());

        $this->request->setParam('format', 'xml')
                      ->setActionName('foo');
        $this->helper->initContext();
        $this->assertEquals('xml', $this->helper->getCurrentContext());

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->request->setParam('format', 'foo')
                      ->setActionName('bogus');
        $this->helper->initContext();
        $this->assertNull($this->helper->getCurrentContext());
    }
}

class Zend_Controller_Action_Helper_AjaxContextTestController extends Zend_Controller_Action
{
    public $ajaxable = array(
        'foo' => array('xml'),
        'bar' => array('xml', 'json'),
        'baz' => array(),
    );
}

class Zend_Controller_Action_Helper_AjaxContextTest_LayoutOverride extends Zend_Layout
{
    public static function resetMvcInstance()
    {
        self::$_mvcInstance = null;
    }
}

// Call Zend_Controller_Action_Helper_AjaxContextTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_AjaxContextTest::main") {
    Zend_Controller_Action_Helper_AjaxContextTest::main();
}
