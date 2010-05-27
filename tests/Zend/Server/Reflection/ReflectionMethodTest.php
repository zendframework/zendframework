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
 * Test case for \Zend\Server\Reflection\ReflectionMethod
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class ReflectionMethodTest extends \PHPUnit_Framework_TestCase
{
    protected $_classRaw;
    protected $_class;
    protected $_method;

    protected function setUp()
    {
        $this->_classRaw = new \ReflectionClass('\Zend\Server\Reflection\Reflection');
        $this->_method   = $this->_classRaw->getMethod('reflectClass');
        $this->_class    = new Reflection\ReflectionClass($this->_classRaw);
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
        $r = new Reflection\ReflectionMethod($this->_class, $this->_method);
        $this->assertTrue($r instanceof Reflection\ReflectionMethod);
        $this->assertTrue($r instanceof Reflection\AbstractFunction);

        $r = new Reflection\ReflectionMethod($this->_class, $this->_method, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    /**
     * getDeclaringClass() test
     *
     * Call as method call
     *
     * Returns: \Zend\Server\Reflection\ReflectionClass
     */
    public function testGetDeclaringClass()
    {
        $r = new Reflection\ReflectionMethod($this->_class, $this->_method);

        $class = $r->getDeclaringClass();

        $this->assertTrue($class instanceof Reflection\ReflectionClass);
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
        $r = new Reflection\ReflectionMethod($this->_class, $this->_method);
        $s = serialize($r);
        $u = unserialize($s);

        $this->assertTrue($u instanceof Reflection\ReflectionMethod);
        $this->assertTrue($u instanceof Reflection\AbstractFunction);
        $this->assertEquals($r->getName(), $u->getName());
        $this->assertEquals($r->getDeclaringClass()->getName(), $u->getDeclaringClass()->getName());
    }


}
