<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Writer;

use ZendTest\Log\TestAsset\ConcreteWriter;
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

    /**
     * @group ZF-6085
     */
    public function testSetFormatter()
    {
        $this->_writer->setFormatter(new SimpleFormatter());
        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->_writer->setFormatter(new \StdClass());
    }

    public function testAddFilter()
    {
        $this->_writer->addFilter(1);
        $this->_writer->addFilter(new RegexFilter('/mess/'));
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException');
        $this->_writer->addFilter(new \StdClass());
    }

    public function testAddMockFilterByName()
    {
        $instance = $this->_writer->addFilter('mock');
        $this->assertTrue($instance instanceof ConcreteWriter);
    }
    
    public function testAddRegexFilterWithParamsByName()
    {
        $instance = $this->_writer->addFilter('regex', array('/mess/'));
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
}
