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
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Server\Reflection;
use Zend\Server\Reflection;

/**
 * Test case for \Zend\Server\Reflection\Prototype
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $class = new \ReflectionClass('\Zend\Server\Reflection\Reflection');
        $method = $class->getMethod('reflectClass');
        $parameters = $method->getParameters();
        $this->_parametersRaw = $parameters;

        $fParameters = array();
        foreach ($parameters as $p) {
            $fParameters[] = new Reflection\Parameter($p);
        }
        $this->_parameters = $fParameters;

        $this->_r = new Reflection\Prototype(new Reflection\ReturnValue('void', 'No return'));
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
    public function test__construct()
    {
        $this->assertTrue($this->_r instanceof Reflection\Prototype);

        try {
            $r1 = new Reflection\Prototype($this->_r->getReturnValue(), $this->_parametersRaw);
            $this->fail('Construction should only accept Z_S_R_Parameters');
        } catch (\Exception $e) {
            // do nothing
        }

        try {
            $r1 = new Reflection\Prototype($this->_r->getReturnValue(), 'string');
            $this->fail('Construction requires an array of parameters');
        } catch (\Exception $e) {
            // do nothing
        }
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
     * Returns: \Zend\Server\Reflection\ReturnValue
     */
    public function testGetReturnValue()
    {
        $this->assertTrue($this->_r->getReturnValue() instanceof Reflection\ReturnValue);
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
            $this->assertTrue($parameter instanceof Reflection\Parameter);
        }

        $this->assertTrue($p === $this->_parameters);
    }
}
