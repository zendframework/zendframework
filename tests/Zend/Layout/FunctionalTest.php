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
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Layout;

/**
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Layout
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase // \Zend\Test\PHPUnit\ControllerTestCase
{


    public function setUp()
    {
        $this->markTestSkipped('Must wait until Zend\Test is converted.');
        return;
        
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->frontController->setControllerDirectory(__DIR__ . '/_files/functional-test-app/controllers/');

        // create an instance of the ErrorHandler so we can make sure it will point to our specially named ErrorController
        $plugin = new \Zend\Controller\Plugin\ErrorHandler();
        $plugin->setErrorHandlerController('zend-layout-functional-test-error')
               ->setErrorHandlerAction('error');
        $this->frontController->registerPlugin($plugin, 100);

        \Zend\Layout\Layout::startMvc(__DIR__ . '/_files/functional-test-app/layouts/');
    }

    public function testMissingViewScriptDoesNotDoubleRender()
    {
        // go to the test controller for this funcitonal test
        $this->dispatch('/zend-layout-functional-test-test/missing-view-script');
        $this->assertEquals(trim($this->response->getBody()), "[DEFAULT_LAYOUT_START]\n(ErrorController::errorAction output)[DEFAULT_LAYOUT_END]");
    }

    public function testMissingViewScriptDoesDoubleRender()
    {
        \Zend\Controller\Action\HelperBroker::getStack()->offsetSet(-91, new \Zend\Controller\Action\Helper\ViewRenderer());
        // go to the test controller for this funcitonal test
        $this->dispatch('/zend-layout-functional-test-test/missing-view-script');
        $this->assertEquals(trim($this->response->getBody()), "[DEFAULT_LAYOUT_START]\n[DEFAULT_LAYOUT_START]\n[DEFAULT_LAYOUT_END]\n(ErrorController::errorAction output)[DEFAULT_LAYOUT_END]");
    }

}

// Call Zend_Layout_FunctionalTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Layout_FunctionalTest::main") {
    \Zend_Layout_FunctionalTest::main();
}
