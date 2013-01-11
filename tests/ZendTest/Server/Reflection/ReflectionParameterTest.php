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
 * Test case for \Zend\Server\Reflection\ReflectionParameter
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @group      Zend_Server
 */
class ReflectionParameterTest extends \PHPUnit_Framework_TestCase
{
    protected function _getParameter()
    {
        $method = new \ReflectionMethod('\Zend\Server\Reflection\ReflectionParameter', 'setType');
        $parameters = $method->getParameters();
        return $parameters[0];
    }

    /**
     * __construct() test
     *
     * Call as method call
     *
     * Expects:
     * - r:
     * - type: Optional; has default;
     * - description: Optional; has default;
     *
     * Returns: void
     */
    public function test__construct()
    {
        $parameter = $this->_getParameter();

        $reflection = new Reflection\ReflectionParameter($parameter);
        $this->assertTrue($reflection instanceof Reflection\ReflectionParameter);
    }

    /**
     * __call() test
     *
     * Call as method call
     *
     * Expects:
     * - method:
     * - args:
     *
     * Returns: mixed
     */
    public function test__call()
    {
        $r = new Reflection\ReflectionParameter($this->_getParameter());

        // just test a few call proxies...
        $this->assertTrue(is_bool($r->allowsNull()));
        $this->assertTrue(is_bool($r->isOptional()));
    }

    /**
     * get/setType() test
     */
    public function testGetSetType()
    {
        $r = new Reflection\ReflectionParameter($this->_getParameter());
        $this->assertEquals('mixed', $r->getType());

        $r->setType('string');
        $this->assertEquals('string', $r->getType());
    }

    /**
     * get/setDescription() test
     */
    public function testGetDescription()
    {
        $r = new Reflection\ReflectionParameter($this->_getParameter());
        $this->assertEquals('', $r->getDescription());

        $r->setDescription('parameter description');
        $this->assertEquals('parameter description', $r->getDescription());
    }

    /**
     * get/setPosition() test
     */
    public function testSetPosition()
    {
        $r = new Reflection\ReflectionParameter($this->_getParameter());
        $this->assertEquals(null, $r->getPosition());

        $r->setPosition(3);
        $this->assertEquals(3, $r->getPosition());
    }
}
