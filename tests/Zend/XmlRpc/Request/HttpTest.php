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
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\XmlRpc\Request;

use Zend\XmlRpc\Request,
    ZendTest\AllTests\StreamWrapper\PHPInput;

/**
 * Test case for Zend\XmlRpc\Request\Http
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class HTTPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup environment
     */
    public function setUp()
    {
        $this->xml =<<<EOX
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>test.userUpdate</methodName>
    <params>
        <param>
            <value><string>blahblahblah</string></value>
        </param>
        <param>
            <value><struct>
                <member>
                    <name>salutation</name>
                    <value><string>Felsenblöcke</string></value>
                </member>
                <member>
                    <name>firstname</name>
                    <value><string>Lépiné</string></value>
                </member>
                <member>
                    <name>lastname</name>
                    <value><string>Géranté</string></value>
                </member>
                <member>
                    <name>company</name>
                    <value><string>Zend Technologies, Inc.</string></value>
                </member>
            </struct></value>
        </param>
    </params>
</methodCall>
EOX;
        $this->request = new Request\Http();
        $this->request->loadXml($this->xml);

        $this->server = $_SERVER;
        foreach ($_SERVER as $key => $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                unset($_SERVER[$key]);
            }
        }
        $_SERVER['HTTP_USER_AGENT']     = 'Zend_XmlRpc_Client';
        $_SERVER['HTTP_HOST']           = 'localhost';
        $_SERVER['HTTP_CONTENT_TYPE']   = 'text/xml';
        $_SERVER['HTTP_CONTENT_LENGTH'] = strlen($this->xml) + 1;
        PHPInput::mockInput($this->xml);
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        $_SERVER = $this->server;
        unset($this->request);
        PHPInput::restoreDefault();
    }

    public function testGetRawRequest()
    {
        $this->assertEquals($this->xml, $this->request->getRawRequest());
    }

    public function testGetHeaders()
    {
        $expected = array(
            'User-Agent'     => 'Zend_XmlRpc_Client',
            'Host'           => 'localhost',
            'Content-Type'   => 'text/xml',
            'Content-Length' => 958
        );
        $this->assertEquals($expected, $this->request->getHeaders());
    }

    public function testGetFullRequest()
    {
        $expected =<<<EOT
User-Agent: Zend_XmlRpc_Client
Host: localhost
Content-Type: text/xml
Content-Length: 958

EOT;
        $expected .= $this->xml;

        $this->assertEquals($expected, $this->request->getFullRequest());
    }

    public function testCanPassInMethodAndParams()
    {
        $request = new Request\Http('foo', array('bar', 'baz'));
    }

    public function testExtendingClassShouldBeAbleToReceiveMethodAndParams()
    {
        $request = new HTTPTestExtension('foo', array('bar', 'baz'));
        $this->assertEquals('foo', $request->method);
        $this->assertEquals(array('bar', 'baz'), $request->params);
    }

    public function testHttpRequestReadsFromPhpInput()
    {
        $this->assertNull(PHPInput::argumentsPassedTo('stream_open'));
        $request = new Request\Http();
        list($path, $mode,) = PHPInput::argumentsPassedTo('stream_open');
        $this->assertSame('php://input', $path);
        $this->assertSame('rb', $mode);
        $this->assertSame($this->xml, $request->getRawRequest());
    }

    public function testHttpRequestGeneratesFaultIfReadFromPhpInputFails()
    {
        PHPInput::methodWillReturn('stream_open', false);
        $request = new Request\Http();
        $this->assertTrue($request->isFault());
        $this->assertSame(630, $request->getFault()->getCode());
    }
}

class HTTPTestExtension extends Request\Http
{
    public function __construct($method = null, $params = null)
    {
        $this->method = $method;
        $this->params = (array) $params;
    }
}
