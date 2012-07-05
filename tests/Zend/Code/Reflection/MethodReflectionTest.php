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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Reflection;
use Zend\Code\Reflection\MethodReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Method
 */
class MethodReflectionTest extends \PHPUnit_Framework_TestCase
{
   public function testDeclaringClassReturn()
    {
        $method = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp1');
        $this->assertInstanceOf('Zend\Code\Reflection\ClassReflection', $method->getDeclaringClass());
    }

    public function testParemeterReturn()
    {
        $method = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp2');
        $parameters = $method->getParameters();
        $this->assertEquals(2, count($parameters));
        $this->assertInstanceOf('Zend\Code\Reflection\ParameterReflection', array_shift($parameters));
    }

    public function testStartLine()
    {
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5', 'doSomething');

        $this->assertEquals(37, $reflectionMethod->getStartLine());
        $this->assertEquals(21, $reflectionMethod->getStartLine(true));
    }

    public function testGetBodyReturnsCorrectBody()
    {
        $body = '        //we need a multi-line method body.
        $assigned = 1;
        $alsoAssigined = 2;
        return \'mixedValue\';';
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6', 'doSomething');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

    public function testGetContentsReturnsCorrectContent()
    {
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5', 'doSomething');
        $this->assertEquals("    {\n\n        return 'mixedValue';\n\n    }\n", $reflectionMethod->getContents(false));
    }

}

