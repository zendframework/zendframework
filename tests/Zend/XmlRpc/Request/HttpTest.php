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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

// Call Zend_XmlRpc_Request_HttpTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_XmlRpc_Request_HttpTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'Zend/XmlRpc/Request/Http.php';

/**
 * Test case for Zend_XmlRpc_Request_Http
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class Zend_XmlRpc_Request_HttpTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_XmlRpc_Request_HttpTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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
        $this->request = new Zend_XmlRpc_Request_Http();
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
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        $_SERVER = $this->server;
        unset($this->request);
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
        try {
            $request = new Zend_XmlRpc_Request_Http('foo', array('bar', 'baz'));
        } catch (Exception $e) {
            $this->fail('Should be able to pass in methods and params to request');
        }
    }

    public function testExtendingClassShouldBeAbleToReceiveMethodAndParams()
    {
        try {
            $request = new Zend_XmlRpc_Request_HttpTest_Extension('foo', array('bar', 'baz'));
        } catch (Exception $e) {
            $this->fail('Should be able to pass in methods and params to request');
        }
        $this->assertEquals('foo', $request->method);
        $this->assertEquals(array('bar', 'baz'), $request->params);
    }
}

class Zend_XmlRpc_Request_HttpTest_Extension extends Zend_XmlRpc_Request_Http
{
    public function __construct($method = null, $params = null)
    {
        $this->method = $method;
        $this->params = (array) $params;
    }
}

// Call Zend_XmlRpc_Request_HttpTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_XmlRpc_Request_HttpTest::main") {
    Zend_XmlRpc_Request_HttpTest::main();
}
