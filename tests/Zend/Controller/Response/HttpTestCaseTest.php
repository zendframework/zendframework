<?php
// Call Zend_Controller_Response_HttpTestCaseTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Response_HttpTestCaseTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Controller_Response_HttpTestCase */
require_once 'Zend/Controller/Response/HttpTestCase.php';

/**
 * Test class for Zend_Controller_Response_HttpTestCase.
 */
class Zend_Controller_Response_HttpTestCaseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Response_HttpTestCaseTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->response = new Zend_Controller_Response_HttpTestCase();
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

// Call Zend_Controller_Response_HttpTestCaseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Response_HttpTestCaseTest::main") {
    Zend_Controller_Response_HttpTestCaseTest::main();
}
