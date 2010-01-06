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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
require_once 'Zend/Tool/Framework/Client/Response.php';

require_once 'Zend/Tool/Framework/Client/Response/ContentDecorator/Separator.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Client
 */
class Zend_Tool_Framework_Client_ResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Client_Response
     */
    protected $_response = null;

    protected $_responseBuffer = array();

    public function setup()
    {
        $this->_response = new Zend_Tool_Framework_Client_Response();
    }

    public function testContentGetterAndSetter()
    {
        $this->_response->setContent('foo');
        $this->assertEquals('foo', $this->_response->getContent());

        $this->_response->setContent('bar');
        $this->assertEquals('bar', $this->_response->getContent());
    }

    public function testContentCanBeAppended()
    {
        $this->_response->setContent('foo');
        $this->assertEquals('foo', $this->_response->getContent());

        $this->_response->setContent('bar');
        $this->assertEquals('bar', $this->_response->getContent());

        $this->_response->appendContent('foo');
        $this->assertEquals('barfoo', $this->_response->getContent());
    }

    public function testContentCallback()
    {
        $this->_response->setContentCallback(array($this, '_responseCallback'));
        $this->_response->appendContent('foo');
        $this->assertEquals('foo', implode('', $this->_responseBuffer));
        $this->_response->appendContent('bar');
        $this->_response->appendContent('baz');
        $this->assertEquals('foo-bar-baz', implode('-', $this->_responseBuffer));
    }

    public function testExceptionHandling()
    {
        $this->assertFalse($this->_response->isException());
        $this->_response->setException(new Exception('my response exception'));
        $this->assertTrue($this->_response->isException());
        $this->assertEquals('my response exception', $this->_response->getException()->getMessage());
    }

    /**
     * @expectedException Zend_Tool_Framework_Client_Exception
     */
    public function testSetCallbackThrowsExceptionOnInvalidCallback()
    {
        $this->_response->setContentCallback(5);
    }

    public function testCastingToString()
    {
        $this->_response->appendContent('foo');
        $this->_response->appendContent('boo');
        $this->assertEquals('fooboo', $this->_response->__toString());
    }

    public function testAddContentDecoratorPersistsDecorators()
    {
        $separator = new Zend_Tool_Framework_Client_Response_ContentDecorator_Separator();
        $this->_response->addContentDecorator($separator);
        $decorators = $this->_response->getContentDecorators();
        $this->assertArrayHasKey('separator', $decorators);
        $this->assertContains($separator, $decorators);
    }

    public function testResponseWillApplyDecorator()
    {
        $separator = new Zend_Tool_Framework_Client_Response_ContentDecorator_Separator();
        $this->_response->addContentDecorator($separator);
        $this->_response->appendContent('foo', array('separator' => true));
        $this->_response->appendContent('boo', array('separator' => true));
        $this->assertEquals('foo' . PHP_EOL . 'boo' . PHP_EOL, $this->_response->__toString());
    }

    public function testResponseWillIgnoreUnknownDecoratorOptions()
    {
        $separator = new Zend_Tool_Framework_Client_Response_ContentDecorator_Separator();
        $this->_response->addContentDecorator($separator);
        $this->_response->appendContent('foo', array('foo' => 'foo'));
        $this->_response->appendContent('boo', array('bar' => 'bar'));
        $this->assertEquals('fooboo', $this->_response->__toString());
    }

    public function testResponseWillApplyDecoratorWithDefaultOptions()
    {
        $separator = new Zend_Tool_Framework_Client_Response_ContentDecorator_Separator();
        $this->_response->addContentDecorator($separator);
        $this->_response->setDefaultDecoratorOptions(array('separator' => true));
        $this->_response->appendContent('foo');
        $this->_response->appendContent('boo');
        $this->assertEquals('foo' . PHP_EOL . 'boo' . PHP_EOL, $this->_response->__toString());
    }

    public function _responseCallback($content)
    {
        $this->_responseBuffer[] = $content;
    }
}
