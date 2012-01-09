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
namespace ZendTest\Controller\Response;

/** Zend_Controller_Response_HTTPTestCase */

/**
 * Test class for Zend_Controller_Response_HTTPTestCase.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Response
 */
class HTTPTestCaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->response = new \Zend\Controller\Response\HttpTestCase();
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

    public function testToStringAndSendResponseShouldNotEchoOutput()
    {
        $this->response->setHeader('X-Foo-Bar', 'baz')
                       ->setBody('Body to emit');
        ob_start();
        $this->response->sendResponse();
        $test = ob_get_clean();
        $this->assertTrue(empty($test));
    }

    public function testSendResponseShouldRenderHeaders()
    {
        $this->response->setHeader('X-Foo-Bar', 'baz')
                       ->setBody('Body to emit');
        $test = $this->response->sendResponse();
        $this->assertContains("X-Foo-Bar: baz\n\nBody to emit", $test);
    }

    public function testOutputBodyShouldReturnStringInsteadOfEchoingOutput()
    {
        $this->response->append('foo', "Foo Content\n")
                       ->append('bar', "Bar Content\n")
                       ->prepend('baz', "Baz Content\n");
        ob_start();
        $content = $this->response->outputBody();
        $test = ob_get_clean();
        $this->assertTrue(empty($test));
        $this->assertFalse(empty($content));
        $this->assertContains("Baz Content\nFoo Content\nBar Content\n", $content, $content);
    }

    public function testSendHeadersShouldReturnArrayOfHeadersInsteadOfSendingHeaders()
    {
        $this->response->setRawHeader('200 OK')
                       ->setHeader('Content-Type', 'text/xml')
                       ->setHeader('Content-Type', 'text/html', true)
                       ->setHeader('X-Foo-Bar', 'baz');
        $test = $this->response->sendHeaders();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test));
        $this->assertNotContains('Content-Type: text/xml', $test);
        $this->assertContains('Content-Type: text/html', $test);
        $this->assertContains('X-Foo-Bar: baz', $test);
        $this->assertContains('200 OK', $test);
    }

    public function testCanSendHeadersShouldAlwaysReturnTrue()
    {
        $this->assertTrue($this->response->canSendHeaders());
    }
}

