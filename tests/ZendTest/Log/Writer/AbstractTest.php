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

use ZendTest\Log\TestAsset\ConcreteWriter;
use ZendTest\Log\TestAsset\ErrorGeneratingWriter;
use Zend\Log\Formatter\Simple as SimpleFormatter;
use Zend\Log\Filter\Regex as RegexFilter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
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
}
