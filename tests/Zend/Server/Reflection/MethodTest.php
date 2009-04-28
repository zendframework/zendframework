<?php
require_once 'Zend/Server/Reflection/Method.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

require_once 'Zend/Server/Reflection.php';

/**
 * Test case for Zend_Server_Reflection_Method
 *
 * @package Zend_Server
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_Server_Reflection_MethodTest extends PHPUnit_Framework_TestCase 
{
    protected $_classRaw;
    protected $_class;
    protected $_method;

    protected function setUp()
    {
        $this->_classRaw = new ReflectionClass('Zend_Server_Reflection');
        $this->_method   = $this->_classRaw->getMethod('reflectClass');
        $this->_class    = new Zend_Server_Reflection_Class($this->_classRaw);
    }

    /**
     * __construct() test
     *
     * Call as method call 
     *
     * Expects:
     * - class: 
     * - r: 
     * - namespace: Optional; 
     * - argv: Optional; has default; 
     * 
     * Returns: void 
     */
    public function test__construct()
    {
        $r = new Zend_Server_Reflection_Method($this->_class, $this->_method);
        $this->assertTrue($r instanceof Zend_Server_Reflection_Method);
        $this->assertTrue($r instanceof Zend_Server_Reflection_Function_Abstract);

        $r = new Zend_Server_Reflection_Method($this->_class, $this->_method, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    /**
     * getDeclaringClass() test
     *
     * Call as method call 
     *
     * Returns: Zend_Server_Reflection_Class 
     */
    public function testGetDeclaringClass()
    {
        $r = new Zend_Server_Reflection_Method($this->_class, $this->_method);

        $class = $r->getDeclaringClass();

        $this->assertTrue($class instanceof Zend_Server_Reflection_Class);
        $this->assertTrue($this->_class === $class);
    }

    /**
     * __wakeup() test
     *
     * Call as method call 
     *
     * Returns: void 
     */
    public function test__wakeup()
    {
        $r = new Zend_Server_Reflection_Method($this->_class, $this->_method);
        $s = serialize($r);
        $u = unserialize($s);

        $this->assertTrue($u instanceof Zend_Server_Reflection_Method);
        $this->assertTrue($u instanceof Zend_Server_Reflection_Function_Abstract);
        $this->assertEquals($r->getName(), $u->getName());
        $this->assertEquals($r->getDeclaringClass()->getName(), $u->getDeclaringClass()->getName());
    }


}
