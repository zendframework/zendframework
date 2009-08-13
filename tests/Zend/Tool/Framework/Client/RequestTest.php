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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper.php
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/**
 * @see Zend_Tool_Framework_Client_Request
 */
require_once 'Zend/Tool/Framework/Client/Request.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Client
 */
class Zend_Tool_Framework_Client_RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Client_Request
     */
    protected $_request = null;
    
    public function setup()
    {
        $this->_request = new Zend_Tool_Framework_Client_Request();
    }
    
    public function testProviderNameGetterAndSetter()
    {
        $this->_request->setProviderName('foo');
        $this->assertEquals('foo', $this->_request->getProviderName());
    }
    
    public function testSpecialtyNameGetterAndSetter()
    {
        $this->_request->setSpecialtyName('foo');
        $this->assertEquals('foo', $this->_request->getSpecialtyName());
    }
    
    public function testActionNameGetterAndSetter()
    {
        $this->_request->setActionName('foo');
        $this->assertEquals('foo', $this->_request->getActionName());
    }
    
    public function testActionParametersGetterAndSetter()
    {
        $this->_request->setActionParameter('foo', 'bar');
        $this->_request->setActionParameter('bar', 'baz');
        $this->assertEquals('bar', $this->_request->getActionParameter('foo'));
        $this->assertArrayHasKey('foo', $this->_request->getActionParameters());
        $this->assertArrayHasKey('bar', $this->_request->getActionParameters());
        $this->assertEquals(2, count($this->_request->getActionParameters()));
    }
    
    public function testProviderParameterGetterAndSetter()
    {
        $this->_request->setProviderParameter('foo', 'bar');
        $this->_request->setProviderParameter('bar', 'baz');
        $this->assertEquals('bar', $this->_request->getProviderParameter('foo'));
        $this->assertArrayHasKey('foo', $this->_request->getProviderParameters());
        $this->assertArrayHasKey('bar', $this->_request->getProviderParameters());
        $this->assertEquals(2, count($this->_request->getProviderParameters()));
    }
    
    public function testPretendGetterAndSetter()
    {
        $this->assertFalse($this->_request->isPretend());
        $this->_request->setPretend(true);
        $this->assertTrue($this->_request->isPretend());
    }
    
    public function testDispatchableGetterAndSetter()
    {
        $this->assertTrue($this->_request->isDispatchable());
        $this->_request->setDispatchable(false);
        $this->assertFalse($this->_request->isDispatchable());
    }
    
    /*
    protected $_providerName = null;
    protected $_specialtyName = null;
    protected $_actionName = null;
    protected $_actionParameters = array();
    protected $_providerParameters = array();
    protected $_isPretend = false;
    protected $_isDispatchable = true;
    */        
    
}
