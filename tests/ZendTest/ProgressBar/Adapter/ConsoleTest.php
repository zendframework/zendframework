<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ProgressBar
 */

namespace ZendTest\ProgressBar\Adapter;

use Zend\ProgressBar\Adapter;

require_once 'MockupStream.php';

/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @group      Zend_ProgressBar
 */
class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        stream_wrapper_register("zendprogressbaradapterconsole", 'ZendTest\ProgressBar\Adapter\MockupStream');
    }

    protected function tearDown()
    {
        stream_wrapper_unregister('zendprogressbaradapterconsole');
    }

    public function testWindowsWidth()
    {
        if (substr(PHP_OS, 0, 3) === 'WIN') {
            $adapter = new Stub();
            $adapter->notify(0, 100, 0, 0, null, null);
            $this->assertEquals(79, strlen($adapter->getLastOutput()));
        } else {
            $this->markTestSkipped('Not testable on non-windows systems');
        }
    }

    public function testStandardOutputStream()
    {
        $adapter = new Stub();

        $this->assertTrue(is_resource($adapter->getOutputStream()));

        $metaData = stream_get_meta_data($adapter->getOutputStream());
        $this->assertEquals('php://stdout', $metaData['uri']);
    }

    public function testManualStandardOutputStream()
    {
        $adapter = new Stub(array('outputStream' => 'php://stdout'));

        $this->assertTrue(is_resource($adapter->getOutputStream()));

        $metaData = stream_get_meta_data($adapter->getOutputStream());
        $this->assertEquals('php://stdout', $metaData['uri']);
    }

    public function testManualErrorOutputStream()
    {
        $adapter = new Stub(array('outputStream' => 'php://stderr'));

        $this->assertTrue(is_resource($adapter->getOutputStream()));

        $metaData = stream_get_meta_data($adapter->getOutputStream());
        $this->assertEquals('php://stderr', $metaData['uri']);
    }

    public function testFixedWidth()
    {
        $adapter = new Stub(array('width' => 30));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('  0% [----------]             ', $adapter->getLastOutput());
    }

    public function testInvalidElement()
    {
        $this->setExpectedException('Zend\ProgressBar\Adapter\Exception\InvalidArgumentException', 'Invalid element found');
        $adapter = new Stub(array('width' => 30, 'elements' => array('foo')));
    }

    public function testCariageReturn()
    {
        $adapter = new Stub(array('width' => 30));
        $adapter->notify(0, 100, 0, 0, null, null);
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals(str_repeat("\x08", 30) . '  0% [----------]             ', $adapter->getLastOutput());
    }

    public function testBarLayout()
    {
        $adapter = new Stub(array('width' => 30));
        $adapter->notify(50, 100, .5, 0, null, null);

        $this->assertContains(' 50% [#####-----]', $adapter->getLastOutput());
    }

    public function testBarOnly()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_BAR)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('[------------------]', $adapter->getLastOutput());
    }

    public function testPercentageOnly()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_PERCENT)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('  0%', $adapter->getLastOutput());
    }

    public function testEtaOnly()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_ETA)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('            ', $adapter->getLastOutput());
    }

    public function testCustomOrder()
    {
        $adapter = new Stub(array('width' => 25, 'elements' =>  array(Adapter\Console::ELEMENT_ETA,
                                                                                                       Adapter\Console::ELEMENT_PERCENT,
                                                                                                       Adapter\Console::ELEMENT_BAR)));
        $adapter->notify(0, 100, 0, 0, null, null);

        $this->assertEquals('               0% [-----]', $adapter->getLastOutput());
    }

    public function testBarStyleIndicator()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_BAR), 'barIndicatorChar' => '>'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[##>---------------]', $adapter->getLastOutput());
    }

    public function testBarStyleIndicatorWide()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_BAR), 'barIndicatorChar' => '[]'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[##[]--------------]', $adapter->getLastOutput());
    }

    public function testBarStyleLeftRightNormal()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_BAR), 'barLeftChar' => '+', 'barRightChar' => ' '));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[++                ]', $adapter->getLastOutput());
    }

    public function testBarStyleLeftRightWide()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_BAR), 'barLeftChar' => '+-', 'barRightChar' => '=-'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[+-=-=-=-=-=-=-=-=-]', $adapter->getLastOutput());
    }

    public function testBarStyleLeftIndicatorRightWide()
    {
        $adapter = new Stub(array('width' => 20, 'elements' => array(Adapter\Console::ELEMENT_BAR), 'barLeftChar' => '+-', 'barIndicatorChar' => '[]', 'barRightChar' => '=-'));
        $adapter->notify(10, 100, .1, 0, null, null);

        $this->assertContains('[+-[]=-=-=-=-=-=-=-]', $adapter->getLastOutput());
    }

    public function testEtaDelayDisplay()
    {
        $adapter = new Stub(array('width' => 100, 'elements' => array(Adapter\Console::ELEMENT_ETA)));

        $adapter->notify(33, 100, .33, 3, null, null);
        $this->assertContains('            ', $adapter->getLastOutput());

        $adapter->notify(66, 100, .66, 6, 3, null);
        $result = preg_match('#ETA 00:00:(\d)+#', $adapter->getLastOutput(), $match);

        $this->assertEquals(1, $result);
    }

    public function testEtaHighValue()
    {
        $adapter = new Stub(array('width' => 100, 'elements' => array(Adapter\Console::ELEMENT_ETA)));

        $adapter->notify(1, 100005, .001, 5, 100000, null);

        $this->assertContains('ETA ??:??:??', $adapter->getLastOutput());
    }

    public function testTextElementDefaultLength()
    {
        $adapter = new Stub(array('width' => 100, 'elements' => array(Adapter\Console::ELEMENT_TEXT, Adapter\Console::ELEMENT_BAR)));
        $adapter->notify(0, 100, 0, 0, null, 'foobar');

        $this->assertContains('foobar               [', $adapter->getLastOutput());
    }

    public function testTextElementCustomLength()
    {
        $adapter = new Stub(array('width' => 100, 'elements' => array(Adapter\Console::ELEMENT_TEXT, Adapter\Console::ELEMENT_BAR), 'textWidth' => 10));
        $adapter->notify(0, 100, 0, 0, null, 'foobar');

        $this->assertContains('foobar     [', $adapter->getLastOutput());
    }

    public function testSetOutputStreamOpen()
    {
        $adapter = new Adapter\Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test1');
        $this->assertArrayHasKey('test1', MockupStream::$tests);
    }

    public function testSetOutputStreamOpenFail()
    {
        $adapter = new Adapter\Console();

        $this->setExpectedException('Zend\ProgressBar\Adapter\Exception\RuntimeException', 'Unable to open stream');
        $adapter->setOutputStream(null);
    }

    public function testSetOutputStreamReplaceStream()
    {
        $adapter = new Adapter\Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test2');
        $this->assertArrayHasKey('test2', MockupStream::$tests);
        $adapter->setOutputStream('zendprogressbaradapterconsole://test3');
        $this->assertArrayHasKey('test3', MockupStream::$tests);
        $this->assertArrayNotHasKey('test2', MockupStream::$tests);
    }

    public function testgetOutputStream()
    {
        $adapter = new Adapter\Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test4');
        $resource = $adapter->getOutputStream();
        fwrite($resource, 'Hello Word!');
        $this->assertEquals('Hello Word!', MockupStream::$tests['test4']);
    }

    public function testgetOutputStreamReturnigStdout()
    {
        $adapter = new Adapter\Console();
        $resource = $adapter->getOutputStream();
        $this->assertTrue(is_resource($resource));
    }

    public function testFinishEol()
    {
        $adapter = new Adapter\Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test5');
        $adapter->finish();
        $this->assertEquals(PHP_EOL, MockupStream::$tests['test5']);
    }

    public function testFinishNone()
    {
        $adapter = new Adapter\Console();
        $adapter->setOutputStream('zendprogressbaradapterconsole://test7');
        $adapter->setFinishAction(Adapter\Console::FINISH_ACTION_NONE);
        $adapter->finish();
        $this->assertEquals('', MockupStream::$tests['test7']);
    }

    public function testSetBarLeftChar()
    {
        $adapter = new Adapter\Console();

        $this->setExpectedException('Zend\ProgressBar\Adapter\Exception\InvalidArgumentException','Character may not be empty');
        $adapter->setBarLeftChar(null);
    }

    public function testSetBarRightChar()
    {
        $adapter = new Adapter\Console();

        $this->setExpectedException('Zend\ProgressBar\Adapter\Exception\InvalidArgumentException','Character may not be empty');
        $adapter->setBarRightChar(null);
    }

    public function testSetInvalidFinishAction()
    {
        $adapter = new Adapter\Console();

        $this->setExpectedException('Zend\ProgressBar\Adapter\Exception\InvalidArgumentException','Invalid finish action specified');
        $adapter->setFinishAction('CUSTOM_FINISH_ACTION');
    }

}

class Stub extends Adapter\Console
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
