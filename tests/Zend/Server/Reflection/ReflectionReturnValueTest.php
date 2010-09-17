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
 */

/**
 * @namespace
 */
namespace ZendTest\Server\Reflection;
use Zend\Server\Reflection;

/**
 * Test case for \Zend\Server\Reflection\ReflectionReturnValue
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class ReflectionReturnValueTest extends \PHPUnit_Framework_TestCase
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
        $obj = new Reflection\ReflectionReturnValue();
        $this->assertTrue($obj instanceof Reflection\ReflectionReturnValue);
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
        $obj = new Reflection\ReflectionReturnValue();
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
        $obj = new Reflection\ReflectionReturnValue();

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
        $obj = new Reflection\ReflectionReturnValue('string', 'Some description');
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
        $obj = new Reflection\ReflectionReturnValue();

        $obj->setDescription('New Description');
        $this->assertEquals('New Description', $obj->getDescription());
    }
}
