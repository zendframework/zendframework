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
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_ProgressBar_Adapter_ConsoleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_ProgressBar_Adapter_ConsoleTest::main");
}

/**
 * Test helper
 */

/**
 * Zend_ProgressBar_Adapter_Console
 */


/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_ProgressBar
 */
class Zend_ProgressBar_Adapter_ConsoleTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        stream_wrapper_register("zendprogressbaradapterconsole", "Zend_ProgressBar_Adapter_Console_MockupStream");
    }

    protected function tearDown()
    {
        stream_wrapper_unregister('zendprogressbaradapterconsole');
    }

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_ProgressBar_Adapter_ConsoleTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testWindowsWidth()
    {
        if (substr(PHP_OS, 0, 3) === 'WIN') {
            $adapter = new Zend_ProgressBar_Adapter_Console_Stub();
            $adapter->notify(0, 100, 0, 0, null, null);
            $this->assertEquals(79, strlen($adapter->getLastOutput()));
        } else {
            $this->markTestSkipped('Not testable on non-windows systems');
        }
    }

    public function testStandardOutputStream()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub();

        $this->assertTrue(is_resource($adapter->getOutputStream()));

        $metaData = stream_get_meta_data($adapter->getOutputStream());
        $this->assertEquals('php://stdout', $metaData['uri']);
    }

    public function testManualStandardOutputStream()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('outputStream' => 'php://stdout'));

        $this->assertTrue(is_resource($adapter->getOutputStream()));

        $metaData = stream_get_meta_data($adapter->getOutputStream());
        $this->assertEquals('php://stdout', $metaData['uri']);
    }

    public function testManualErrorOutputStream()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('outputStream' => 'php://stderr'));

        $this->assertTrue(is_resource($adapter->getOutputStream()));

        $metaData = stream_get_meta_data($adapter->getOutputStream());
        $this->assertEquals('php://stderr', $metaData['uri']);
    }

    public function testFixedWidth()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 30));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('  0% [----------]             ', $adapter->getLastOutput());
    }

    public function testInvalidElement()
    {
        try {
            $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 30, 'elements' => array('foo')));
            $adapter->notify(0, 100, 0, 0, null, null);

            $this->fail('An expected Zend_ProgressBar_Adapter_Exception has not been raised');
        } catch (Zend_ProgressBar_Adapter_Exception $expected) {
            $this->assertContains('Invalid element found in $elements array', $expected->getMessage());
        }
    }

    public function testCariageReturn()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 30));
        $adapter->notify(0, 100, 0, 0, null, null);
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals(str_repeat("\x08", 30) . '  0% [----------]             ', $adapter->getLastOutput());
    }

    public function testBarLayout()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 30));
        $adapter->notify(50, 100, .5, 0, null, null);

        $this->assertContains(' 50% [#####-----]', $adapter->getLastOutput());
    }

    public function testBarOnly()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('[------------------]', $adapter->getLastOutput());
    }

    public function testPercentageOnly()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('  0%', $adapter->getLastOutput());
    }

    public function testEtaOnly()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_ETA)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('            ', $adapter->getLastOutput());
    }

    public function testCustomOrder()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 25, 'elements' =>  array(Zend_ProgressBar_Adapter_Console::ELEMENT_ETA,
                                                                                                       Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                                                                                       Zend_ProgressBar_Adapter_Console::ELEMENT_BAR)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('               0% [-----]', $adapter->getLastOutput());
    }

    public function testBarStyleIndicator()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR), 'barIndicatorChar' => '>'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[##>---------------]', $adapter->getLastOutput());
    }

    public function testBarStyleIndicatorWide()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR), 'barIndicatorChar' => '[]'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[##[]--------------]', $adapter->getLastOutput());
    }

    public function testBarStyleLeftRightNormal()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR), 'barLeftChar' => '+', 'barRightChar' => ' '));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[++                ]', $adapter->getLastOutput());
    }

    public function testBarStyleLeftRightWide()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR), 'barLeftChar' => '+-', 'barRightChar' => '=-'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[+-=-=-=-=-=-=-=-=-]', $adapter->getLastOutput());
    }

    public function testBarStyleLeftIndicatorRightWide()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 20, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_BAR), 'barLeftChar' => '+-', 'barIndicatorChar' => '[]', 'barRightChar' => '=-'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[+-[]=-=-=-=-=-=-=-]', $adapter->getLastOutput());
    }

    public function testEtaDelayDisplay()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 100, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_ETA)));

        $adapter->notify(33, 100, .33, 3, null, null);
        $this->assertContains('            ', $adapter->getLastOutput());

        $adapter->notify(66, 100, .66, 6, 3, null);
        $result = preg_match('#ETA 00:00:(\d)+#', $adapter->getLastOutput(), $match);

        $this->assertEquals(1, $result);
    }

    public function testEtaHighValue()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 100, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_ETA)));

        $adapter->notify(1, 100005, .001, 5, 100000, null);

        $this->assertContains('ETA ??:??:??', $adapter->getLastOutput());
    }

    public function testTextElementDefaultLength()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 100, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT, Zend_ProgressBar_Adapter_Console::ELEMENT_BAR)));
        $adapter->notify(0, 100, 0, 0, null, 'foobar');

        $this->assertContains('foobar               [', $adapter->getLastOutput());
    }

    public function testTextElementCustomLength()
    {
        $adapter = new Zend_ProgressBar_Adapter_Console_Stub(array('width' => 100, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT, Zend_ProgressBar_Adapter_Console::ELEMENT_BAR), 'textWidth' => 10));
        $adapter->notify(0, 100, 0, 0, null, 'foobar');

        $this->assertContains('foobar     [', $adapter->getLastOutput());
    }

    public function testSetOutputStreamOpen() {
        $adapter = new Zend_ProgressBar_Adapter_Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test1');
        $this->assertArrayHasKey('test1', Zend_ProgressBar_Adapter_Console_MockupStream::$tests);
    }

    public function testSetOutputStreamOpenFail() {
        try {
            $adapter = new Zend_ProgressBar_Adapter_Console();
            $adapter->setOutputStream(null);
            $this->fail('Expected Zend_ProgressBar_Adapter_Exception');
        } catch (Zend_ProgressBar_Adapter_Exception $e) {
        }
    }

    public function testSetOutputStreamReplaceStream() {
        $adapter = new Zend_ProgressBar_Adapter_Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test2');
        $this->assertArrayHasKey('test2', Zend_ProgressBar_Adapter_Console_MockupStream::$tests);
        $adapter->setOutputStream('zendprogressbaradapterconsole://test3');
        $this->assertArrayHasKey('test3', Zend_ProgressBar_Adapter_Console_MockupStream::$tests);
        $this->assertArrayNotHasKey('test2', Zend_ProgressBar_Adapter_Console_MockupStream::$tests);
    }

    public function testgetOutputStream() {
        $adapter = new Zend_ProgressBar_Adapter_Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test4');
        $resource = $adapter->getOutputStream();
        fwrite($resource, 'Hello Word!');
        $this->assertEquals('Hello Word!', Zend_ProgressBar_Adapter_Console_MockupStream::$tests['test4']);
    }

    public function testgetOutputStreamReturnigStdout() {
        $adapter = new Zend_ProgressBar_Adapter_Console();
        $resource = $adapter->getOutputStream();
        $this->assertTrue(is_resource($resource));
    }

    public function testFinishEol() {
        $adapter = new Zend_ProgressBar_Adapter_Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test5');
        $adapter->finish();
        $this->assertEquals(PHP_EOL, Zend_ProgressBar_Adapter_Console_MockupStream::$tests['test5']);
    }

    public function testFinishNone() {
        $adapter = new Zend_ProgressBar_Adapter_Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test7');
        $adapter->setFinishAction(Zend_ProgressBar_Adapter_Console::FINISH_ACTION_NONE);
        $adapter->finish();
        $this->assertEquals('', Zend_ProgressBar_Adapter_Console_MockupStream::$tests['test7']);
    }

    public function testSetBarLeftChar() {
        try {
            $adapter = new Zend_ProgressBar_Adapter_Console();
            $adapter->setBarLeftChar(null);
            $this->fail('Expected Zend_ProgressBar_Adapter_Exception');
        } catch (Zend_ProgressBar_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Character may not be empty');
        }
    }

    public function testSetBarRightChar() {
        try {
            $adapter = new Zend_ProgressBar_Adapter_Console();
            $adapter->setBarRightChar(null);
            $this->fail('Expected Zend_ProgressBar_Adapter_Exception');
        } catch (Zend_ProgressBar_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Character may not be empty');
        }
    }

    public function testSetInvalidFinishAction() {
        try {
            $adapter = new Zend_ProgressBar_Adapter_Console();
            $adapter->setFinishAction('CUSTOM_FINISH_ACTION');
            $this->fail('Expected Zend_ProgressBar_Adapter_Exception');
        } catch (Zend_ProgressBar_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid finish action specified');
        }
    }

}

class Zend_ProgressBar_Adapter_Console_Stub extends Zend_ProgressBar_Adapter_Console
{
    protected $_lastOutput = null;

    public function getLastOutput()
    {
        return $this->_lastOutput;
    }

    protected function _outputData($data)
    {
        $this->_lastOutput = $data;
    }
}

// Call Zend_ProgressBar_Adapter_ConsoleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_ProgressBar_Adapert_ConsoleTest::main") {
    Zend_ProgressBar_Adapter_ConsoleTest::main();
}
