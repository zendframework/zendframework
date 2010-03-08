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
 * Test case for Zend_Server_Reflection_ReturnValue
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
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
