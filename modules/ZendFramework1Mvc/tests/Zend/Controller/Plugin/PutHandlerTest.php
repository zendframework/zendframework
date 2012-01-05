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
namespace ZendTest\Controller\Plugin;

/**
 * Test class for Zend_Controller_Plugin_PutHandler.
 */
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Plugin
 */
class PutHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Request object
     * @var Zend_Controller_Request_HTTP
     */
    public $request;

    /**
     * Error handler plugin
     * @var Zend_Controller_Plugin_PutHandler
     */
    public $plugin;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        \Zend\Controller\Front::getInstance()->resetInstance();
        $this->request  = new \Zend\Controller\Request\HttpTestCase();
        $this->plugin   = new \Zend\Controller\Plugin\PutHandler();

        $this->plugin->setRequest($this->request);
    }

    public function test_marshall_PUT_body_as_params()
    {
        $this->request->setMethod('PUT');
        $this->request->setRawBody('param1=value1&param2=value2');
        $this->plugin->preDispatch($this->request);

        $this->assertEquals('value1', $this->request->getParam('param1'));
        $this->assertEquals('value2', $this->request->getParam('param2'));
    }
}
