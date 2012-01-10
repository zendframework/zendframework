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
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Rest;

use Zend\Controller\Request\HttpTestCase as Request,
    Zend\Controller\Response\HttpTestCase as Response;

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Rest
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $_testController;

    public function setUp()
    {
        $request  = new Request();
        $response = new Response();
        $this->_testController = new TestAsset\TestController($request, $response);
    }

    public function test_action_methods()
    {
        $this->_testController->indexAction();
        $this->assertEquals('indexAction', $this->_testController->testValue);
        $this->_testController->getAction();
        $this->assertEquals('getAction', $this->_testController->testValue);
        $this->_testController->postAction();
        $this->assertEquals('postAction', $this->_testController->testValue);
        $this->_testController->putAction();
        $this->assertEquals('putAction', $this->_testController->testValue);
        $this->_testController->deleteAction();
        $this->assertEquals('deleteAction', $this->_testController->testValue);
    }
}
