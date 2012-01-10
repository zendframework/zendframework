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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;
use Zend\Layout;

/**
 * Test class for Zend_View_Helper_Json
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class JSONTest extends \PHPUnit_Framework_TestCase
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

        $this->response = new \Zend\Controller\Response\Http();
        $this->response->headersSentThrowsException = false;

        $front = \Zend\Controller\Front::getInstance();
        $front->resetInstance();
        $front->setResponse($this->response);

        $this->helper = new \Zend\View\Helper\Json();
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

    public function verifyJsonHeader()
    {
        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/json', $value);
    }

    public function testJsonHelperSetsResponseHeader()
    {
        $json = $this->helper->__invoke('foobar');
        $this->verifyJsonHeader();
    }

    public function testJsonHelperReturnsJsonEncodedString()
    {
        $data = $this->helper->__invoke('foobar');
        $this->assertTrue(is_string($data));
        $this->assertEquals('foobar', \Zend\Json\Json::decode($data));
    }

    public function testJsonHelperDisablesLayoutsByDefault()
    {
        $layout = Layout\Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $this->testJsonHelperReturnsJsonEncodedString();
        $this->assertFalse($layout->isEnabled());
    }

    public function testJsonHelperDoesNotDisableLayoutsWhenKeepLayoutFlagTrue()
    {
        $layout = Layout\Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        
        $data = $this->helper->__invoke(array('foobar'), true);
        $this->assertTrue($layout->isEnabled());
    }
}
