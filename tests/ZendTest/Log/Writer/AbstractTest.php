<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Writer;

use ReflectionObject;
use ZendTest\Log\TestAsset\ConcreteWriter;
use ZendTest\Log\TestAsset\ErrorGeneratingWriter;
use Zend\Log\Formatter\Simple as SimpleFormatter;
use Zend\Log\Filter\Regex as RegexFilter;

/**
 * @group      Zend_Log
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    protected $_writer;

    protected function setUp()
    {
        $this->_writer = new ConcreteWriter();
    }

    public function testSetSimpleFormatterByName()
    {
        $instance = $this->_writer->setFormatter('simple');
        $this->assertAttributeInstanceOf('Zend\Log\Formatter\Simple', 'formatter', $instance);
    }

    public function testAddFilter()
    {
        $this->_writer->addFilter(1);
        $this->_writer->addFilter(new RegexFilter('/mess/'));
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException');
        $this->_writer->addFilter(new \stdClass());
    }

    public function testAddMockFilterByName()
    {
        $instance = $this->_writer->addFilter('mock');
        $this->assertTrue($instance instanceof ConcreteWriter);
    }

    public function testAddRegexFilterWithParamsByName()
    {
        $instance = $this->_writer->addFilter('regex', array( 'regex' => '/mess/' ));
        $this->assertTrue($instance instanceof ConcreteWriter);
    }

    /**
     * @group ZF-8953
     */
    public function testFluentInterface()
    {
        $instance = $this->_writer->addFilter(1)
                                  ->setFormatter(new SimpleFormatter());

        $this->assertTrue($instance instanceof ConcreteWriter);
    }

    public function testConvertErrorsToException()
    {
        $writer = new ErrorGeneratingWriter();
        $this->setExpectedException('Zend\Log\Exception\RuntimeException');
        $writer->write(array('message' => 'test'));

        $writer->setConvertWriteErrorsToExceptions(false);
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');
        $writer->write(array('message' => 'test'));
    }

    public function testConstructorWithOptions()
    {
        $options = array('filters' => array(
                             array(
                                 'name' => 'mock',
                             ),
                             array(
                                 'name' => 'priority',
                                 'options' => array(
                                     'priority' => 3,
                                 ),
                             ),
                         ),
                        'formatter' => array(
                             'name' => 'base',
                         ),
                    );

        $writer = new ConcreteWriter($options);

        $this->assertAttributeInstanceOf('Zend\Log\Formatter\Base', 'formatter', $writer);

        $filters = $this->readAttribute($writer, 'filters');
        $this->assertCount(2, $filters);

        $this->assertInstanceOf('Zend\Log\Filter\Priority', $filters[1]);
        $this->assertEquals(3, $this->readAttribute($filters[1], 'priority'));
    }

    public function testConstructorWithPriorityFilter()
    {
        // Accept an int as a PriorityFilter
        $writer = new ConcreteWriter(array('filters' => 3));
        $filters = $this->readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertInstanceOf('Zend\Log\Filter\Priority', $filters[0]);
        $this->assertEquals(3, $this->readAttribute($filters[0], 'priority'));

        // Accept an int in an array of filters as a PriorityFilter
        $options = array('filters' => array(3, array('name' => 'mock')));

        $writer = new ConcreteWriter($options);
        $filters = $this->readAttribute($writer, 'filters');
        $this->assertCount(2, $filters);
        $this->assertInstanceOf('Zend\Log\Filter\Priority', $filters[0]);
        $this->assertEquals(3, $this->readAttribute($filters[0], 'priority'));
        $this->assertInstanceOf('Zend\Log\Filter\Mock', $filters[1]);
    }

    /**
     * @covers Zend\Log\Writer\AbstractWriter::getFormatter
     */
    public function testFormatterDefaultsToNull()
    {
        $r = new ReflectionObject($this->_writer);
        $m = $r->getMethod('getFormatter');
        $m->setAccessible(true);
        $this->assertNull($m->invoke($this->_writer));
    }

    /**
     * @covers Zend\Log\Writer\AbstractWriter::getFormatter
     * @covers Zend\Log\Writer\AbstractWriter::setFormatter
     */
    public function testCanSetFormatter()
    {
        $formatter = new SimpleFormatter;
        $this->_writer->setFormatter($formatter);

        $r = new ReflectionObject($this->_writer);
        $m = $r->getMethod('getFormatter');
        $m->setAccessible(true);
        $this->assertSame($formatter, $m->invoke($this->_writer));
    }

    /**
     * @covers Zend\Log\Writer\AbstractWriter::hasFormatter
     */
    public function testHasFormatter()
    {
        $r = new ReflectionObject($this->_writer);
        $m = $r->getMethod('hasFormatter');
        $m->setAccessible(true);
        $this->assertFalse($m->invoke($this->_writer));

        $this->_writer->setFormatter(new SimpleFormatter);
        $this->assertTrue($m->invoke($this->_writer));
    }
}
