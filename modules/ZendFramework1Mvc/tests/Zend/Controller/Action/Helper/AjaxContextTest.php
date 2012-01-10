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

use Zend\Controller\Front as FrontController,
    Zend\Layout;

/**
 * Test class for Zend_Controller_Action_Helper_AjaxContext.
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
class AjaxContextTest extends \PHPUnit_Framework_TestCase
{


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

        \Zend\Layout\Layout::resetMvcInstance();

        $this->front = \Zend\Controller\Front::getInstance();
        $this->front->resetInstance();
        $this->front->addModuleDirectory(__DIR__ . '/../../_files/modules');
        $this->broker = $this->front->getHelperBroker();

        $this->layout = Layout\Layout::startMvc();

        $this->helper = new \Zend\Controller\Action\Helper\AjaxContext();

        $this->request = new \Zend\Controller\Request\Http();
        $this->response = new \Zend\Controller\Response\Cli();

        $this->front->setRequest($this->request)->setResponse($this->response);
        $this->view = new \Zend\View\PhpRenderer();
        $this->viewRenderer = $this->broker->load('viewRenderer');
        $this->viewRenderer->setView($this->view);

        $this->controller = new AjaxContextTestController(
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

class AjaxContextTestController extends \Zend\Controller\Action
{
    public $ajaxable = array(
        'foo' => array('xml'),
        'bar' => array('xml', 'json'),
        'baz' => array(),
    );
}
