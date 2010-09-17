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
 * Test case for \Zend\Server\Reflection\ClassReflection
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * __construct() test
     *
     * Call as method call
     *
     * Expects:
     * - reflection:
     * - namespace: Optional;
     * - argv: Optional; has default;
     *
     * Returns: void
     */
    public function test__construct()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Zend\Server\Reflection\Reflection'));
        $this->assertTrue($r instanceof Reflection\ReflectionClass);
        $this->assertEquals('', $r->getNamespace());

        $methods = $r->getMethods();
        $this->assertTrue(is_array($methods));
        foreach ($methods as $m) {
            $this->assertTrue($m instanceof Reflection\ReflectionMethod);
        }

        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Zend\Server\Reflection\Reflection'), 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
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
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Zend\Server\Reflection\Reflection'));
        $this->assertTrue(is_string($r->getName()));
        $this->assertEquals('Zend\Server\Reflection\Reflection', $r->getName());
    }

    /**
     * test __get/set
     */
    public function testGetSet()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Zend\Server\Reflection\Reflection'));
        $r->system = true;
        $this->assertTrue($r->system);
    }

    /**
     * getMethods() test
     *
     * Call as method call
     *
     * Returns: array
     */
    public function testGetMethods()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Zend\Server\Reflection\Reflection'));

        $methods = $r->getMethods();
        $this->assertTrue(is_array($methods));
        foreach ($methods as $m) {
            $this->assertTrue($m instanceof Reflection\ReflectionMethod);
        }
    }

    /**
     * namespace test
     */
    public function testGetNamespace()
    {
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Zend\Server\Reflection\Reflection'));
        $this->assertEquals('', $r->getNamespace());
        $r->setNamespace('namespace');
        $this->assertEquals('namespace', $r->getNamespace());
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
        $r = new Reflection\ReflectionClass(new \ReflectionClass('\Zend\Server\Reflection\Reflection'));
        $s = serialize($r);
        $u = unserialize($s);

        $this->assertTrue($u instanceof Reflection\ReflectionClass);
        $this->assertEquals('', $u->getNamespace());
        $this->assertEquals($r->getName(), $u->getName());
        $rMethods = $r->getMethods();
        $uMethods = $r->getMethods();

        $this->assertEquals(count($rMethods), count($uMethods));
    }
}
