<?php
require_once 'Zend/Server/Reflection/ReturnValue.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

/**
 * Test case for Zend_Server_Reflection_ReturnValue
 *
 * @package Zend_Server
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_Server_Reflection_ReturnValueTest extends PHPUnit_Framework_TestCase 
{
    /**
     * __construct() test
     *
     * Call as method call 
     *
     * Expects:
     * - type: Optional; has default; 
     * - description: Optional; has default; 
     * 
     * Returns: void 
     */
    public function test__construct()
    {
        $obj = new Zend_Server_Reflection_ReturnValue();
        $this->assertTrue($obj instanceof Zend_Server_Reflection_ReturnValue);
    }

    /**
     * getType() test
     *
     * Call as method call 
     *
     * Returns: string 
     */
    public function testGetType()
    {
        $obj = new Zend_Server_Reflection_ReturnValue();
        $this->assertEquals('mixed', $obj->getType());

        $obj->setType('array');
        $this->assertEquals('array', $obj->getType());
    }

    /**
     * setType() test
     *
     * Call as method call 
     *
     * Expects:
     * - type: 
     * 
     * Returns: void 
     */
    public function testSetType()
    {
        $obj = new Zend_Server_Reflection_ReturnValue();

        $obj->setType('array');
        $this->assertEquals('array', $obj->getType());
    }

    /**
     * getDescription() test
     *
     * Call as method call 
     *
     * Returns: string 
     */
    public function testGetDescription()
    {
        $obj = new Zend_Server_Reflection_ReturnValue('string', 'Some description');
        $this->assertEquals('Some description', $obj->getDescription());

        $obj->setDescription('New Description');
        $this->assertEquals('New Description', $obj->getDescription());
    }

    /**
     * setDescription() test
     *
     * Call as method call 
     *
     * Expects:
     * - description: 
     * 
     * Returns: void 
     */
    public function testSetDescription()
    {
        $obj = new Zend_Server_Reflection_ReturnValue();

        $obj->setDescription('New Description');
        $this->assertEquals('New Description', $obj->getDescription());
    }
}
