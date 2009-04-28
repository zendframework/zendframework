<?php
require_once 'Zend/Server/Reflection/Prototype.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

require_once 'Zend/Server/Reflection.php';
require_once 'Zend/Server/Reflection/Parameter.php';
require_once 'Zend/Server/Reflection/ReturnValue.php';

/**
 * Test case for Zend_Server_Reflection_Prototype
 *
 * @package Zend_Server
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_Server_Reflection_PrototypeTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_Server_Reflection_Prototype object
     * @var Zend_Server_Reflection_Prototype
     */
    protected $_r;

    /**
     * Array of ReflectionParameters
     * @var array 
     */
    protected $_parametersRaw;

    /**
     * Array of Zend_Server_Reflection_Parameters
     * @var array 
     */
    protected $_parameters;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $class = new ReflectionClass('Zend_Server_Reflection');
        $method = $class->getMethod('reflectClass');
        $parameters = $method->getParameters();
        $this->_parametersRaw = $parameters;

        $fParameters = array();
        foreach ($parameters as $p) {
            $fParameters[] = new Zend_Server_Reflection_Parameter($p);
        }
        $this->_parameters = $fParameters;

        $this->_r = new Zend_Server_Reflection_Prototype(new Zend_Server_Reflection_ReturnValue('void', 'No return'));
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
        $this->assertTrue($this->_r instanceof Zend_Server_Reflection_Prototype);

        try {
            $r1 = new Zend_Server_Reflection_Prototype($this->_r->getReturnValue(), $this->_parametersRaw);
            $this->fail('Construction should only accept Z_S_R_Parameters');
        } catch (Exception $e) {
            // do nothing
        }

        try {
            $r1 = new Zend_Server_Reflection_Prototype($this->_r->getReturnValue(), 'string');
            $this->fail('Construction requires an array of parameters');
        } catch (Exception $e) {
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
     * Returns: Zend_Server_Reflection_ReturnValue 
     */
    public function testGetReturnValue()
    {
        $this->assertTrue($this->_r->getReturnValue() instanceof Zend_Server_Reflection_ReturnValue);
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
        $r = new Zend_Server_Reflection_Prototype($this->_r->getReturnValue(), $this->_parameters);
        $p = $r->getParameters();

        $this->assertTrue(is_array($p));
        foreach ($p as $parameter) {
            $this->assertTrue($parameter instanceof Zend_Server_Reflection_Parameter);
        }

        $this->assertTrue($p === $this->_parameters);
    }
}
