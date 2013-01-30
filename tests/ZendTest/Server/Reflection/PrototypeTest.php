<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Server
 */

namespace ZendTest\Server\Reflection;

use Zend\Server\Reflection;

/**
 * Test case for \Zend\Server\Reflection\Prototype
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @group      Zend_Server
 */
class PrototypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * \Zend\Server\Reflection\Prototype object
     * @var \Zend\Server\Reflection\Prototype
     */
    protected $_r;

    /**
     * Array of ReflectionParameters
     * @var array
     */
    protected $_parametersRaw;

    /**
     * Array of \Zend\Server\Reflection\Parameters
     * @var array
     */
    protected $_parameters;

    /**
     * Setup environment
     */
    public function setUp()
    {
        $class = new \ReflectionClass('\Zend\Server\Reflection');
        $method = $class->getMethod('reflectClass');
        $parameters = $method->getParameters();
        $this->_parametersRaw = $parameters;

        $fParameters = array();
        foreach ($parameters as $p) {
            $fParameters[] = new Reflection\ReflectionParameter($p);
        }
        $this->_parameters = $fParameters;

        $this->_r = new Reflection\Prototype(new Reflection\ReflectionReturnValue('void', 'No return'));
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->_r);
        unset($this->_parameters);
        unset($this->_parametersRaw);
    }

    /**
     * __construct() test
     *
     * Call as method call
     *
     * Expects:
     * - return:
     * - params: Optional;
     *
     * Returns: void
     */
    public function testConstructWorks()
    {
        $this->assertTrue($this->_r instanceof Reflection\Prototype);
    }

    public function testConstructionThrowsExceptionOnInvalidParam()
    {
        $this->setExpectedException('Zend\Server\Reflection\Exception\InvalidArgumentException', 'One or more params are invalid');
        $r1 = new Reflection\Prototype($this->_r->getReturnValue(), $this->_parametersRaw);
    }

    /**
     * getReturnType() test
     *
     * Call as method call
     *
     * Returns: string
     */
    public function testGetReturnType()
    {
        $this->assertEquals('void', $this->_r->getReturnType());
    }

    /**
     * getReturnValue() test
     *
     * Call as method call
     *
     * Returns: \Zend\Server\Reflection\ReflectionReturnValue
     */
    public function testGetReturnValue()
    {
        $this->assertTrue($this->_r->getReturnValue() instanceof Reflection\ReflectionReturnValue);
    }

    /**
     * getParameters() test
     *
     * Call as method call
     *
     * Returns: array
     */
    public function testGetParameters()
    {
        $r = new Reflection\Prototype($this->_r->getReturnValue(), $this->_parameters);
        $p = $r->getParameters();

        $this->assertTrue(is_array($p));
        foreach ($p as $parameter) {
            $this->assertTrue($parameter instanceof Reflection\ReflectionParameter);
        }

        $this->assertTrue($p === $this->_parameters);
    }
}
