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
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Reflection;
use Zend\Reflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Method
 */
class ReflectionMethodTest extends \PHPUnit_Framework_TestCase
{
   public function testDeclaringClassReturn()
    {
        $method = new Reflection\ReflectionMethod('ZendTest\Reflection\TestAsset\TestSampleClass2', 'getProp1');
        $this->assertEquals(get_class($method->getDeclaringClass()), 'Zend\Reflection\ReflectionClass');
    }

    public function testParemeterReturn()
    {
        $method = new Reflection\ReflectionMethod('ZendTest\Reflection\TestAsset\TestSampleClass2', 'getProp2');
        $parameters = $method->getParameters();
        $this->assertEquals(count($parameters), 2);
        $this->assertEquals(get_class(array_shift($parameters)), 'Zend\Reflection\ReflectionParameter');
    }

    public function testStartLine()
    {
        $reflectionMethod = new Reflection\ReflectionMethod('ZendTest\Reflection\TestAsset\TestSampleClass5', 'doSomething');

        $this->assertEquals($reflectionMethod->getStartLine(), 35);
        $this->assertEquals($reflectionMethod->getStartLine(true), 19);
    }

    public function testGetBodyReturnsCorrectBody()
    {
        $body = '        //we need a multi-line method body.
        $assigned = 1;
        $alsoAssigined = 2;
        return \'mixedValue\';';
        $reflectionMethod = new Reflection\ReflectionMethod('ZendTest\Reflection\TestAsset\TestSampleClass6', 'doSomething');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

    public function testGetContentsReturnsCorrectContent()
    {
        $reflectionMethod = new Reflection\ReflectionMethod('ZendTest\Reflection\TestAsset\TestSampleClass5', 'doSomething');
        $this->assertEquals("    {\n\n        return 'mixedValue';\n\n    }\n", $reflectionMethod->getContents(false));
    }

}

