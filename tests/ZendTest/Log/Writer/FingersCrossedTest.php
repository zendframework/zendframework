<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
* @package   Zend_Log
*/

namespace ZendTest\Log\Writer;

use Zend\Log\Writer\FingersCrossed as FingersCrossedWriter;
use Zend\Log\Writer\Mock as MockWriter;
use Zend\Log\Logger;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class FingersCrossedTest extends \PHPUnit_Framework_TestCase
{
    public function testBuffering()
    {
        $wrappedWriter = new MockWriter();
        $writer = new FingersCrossedWriter($wrappedWriter, 2);

        $writer->write(array('priority' => 3, 'message' => 'foo'));

        $this->assertSame(count($wrappedWriter->events), 0);
    }

    public function testFlushing()
    {
        $wrappedWriter = new MockWriter();
        $writer = new FingersCrossedWriter($wrappedWriter, 2);

        $writer->write(array('priority' => 3, 'message' => 'foo'));
        $writer->write(array('priority' => 1, 'message' => 'bar'));

        $this->assertSame(count($wrappedWriter->events), 2);
    }

    public function testAfterFlushing()
    {
        $wrappedWriter = new MockWriter();
        $writer = new FingersCrossedWriter($wrappedWriter, 2);

        $writer->write(array('priority' => 3, 'message' => 'foo'));
        $writer->write(array('priority' => 1, 'message' => 'bar'));
        $writer->write(array('priority' => 3, 'message' => 'bar'));

        $this->assertSame(count($wrappedWriter->events), 3);
    }

    public function setWriterByName()
    {
        $writer = new FingersCrossedWriter('mock');
        $this->assertAttributeInstanceOf('Zend\Log\Writer\Mock', 'writer', $writer);
    }

    public function testConstructorOptions()
    {
        $options = array('writer' => 'mock', 'priority' => 3);
        $writer = new FingersCrossedWriter($options);
        $this->assertAttributeInstanceOf('Zend\Log\Writer\Mock', 'writer', $writer);

        $filters = $this->readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertInstanceOf('Zend\Log\Filter\Priority', $filters[0]);
        $this->assertAttributeEquals(3, 'priority', $filters[0]);
    }

    public function testFormattingIsNotSupported()
    {
        $options = array('writer' => 'mock', 'priority' => 3);
        $writer = new FingersCrossedWriter($options);

        $writer->setFormatter($this->getMock('Zend\Log\Formatter\FormatterInterface'));
        $this->assertAttributeEmpty('formatter', $writer);
    }
}
