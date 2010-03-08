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
 * Test case for Zend_Server_Reflection_Parameter
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Reflection_ParameterTest extends PHPUnit_Framework_TestCase
{
    protected function _getParameter()
    {
        $method = new ReflectionMethod('Zend_Server_Reflection_Parameter', 'setType');
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

        $reflection = new Zend_Server_Reflection_Parameter($parameter);
        $this->assertTrue($reflection instanceof Zend_Server_Reflection_Parameter);
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
        $r = new Zend_Server_Reflection_Parameter($this->_getParameter());

        // just test a few call proxies...
        $this->assertTrue(is_bool($r->allowsNull()));
        $this->assertTrue(is_bool($r->isOptional()));
    }

    /**
     * get/setType() test
     */
    public function testGetSetType()
    {
        $r = new Zend_Server_Reflection_Parameter($this->_getParameter());
        $this->assertEquals('mixed', $r->getType());

        $r->setType('string');
        $this->assertEquals('string', $r->getType());
    }

    /**
     * get/setDescription() test
     */
    public function testGetDescription()
    {
        $r = new Zend_Server_Reflection_Parameter($this->_getParameter());
        $this->assertEquals('', $r->getDescription());

        $r->setDescription('parameter description');
        $this->assertEquals('parameter description', $r->getDescription());
    }

    /**
     * get/setPosition() test
     */
    public function testSetPosition()
    {
        $r = new Zend_Server_Reflection_Parameter($this->_getParameter());
        $this->assertEquals(null, $r->getPosition());

        $r->setPosition(3);
        $this->assertEquals(3, $r->getPosition());
    }
}
