<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\MethodReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
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

    public function testInternalFunctionBodyReturn()
    {
        $reflectionMethod = new MethodReflection('DOMDocument', 'validate');

        $this->setExpectedException('Zend\Code\Reflection\Exception\InvalidArgumentException');
        $body = $reflectionMethod->getBody();
    }

    public function testGetBodyReturnsCorrectBody()
    {
        $body = '
        //we need a multi-line method body.
        $assigned = 1;
        $alsoAssigined = 2;
        return \'mixedValue\';
    ';
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6', 'doSomething');
        $this->assertEquals($body, $reflectionMethod->getBody());

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomething');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'doSomething';");

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomethingElse');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'doSomethingElse';");

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomethingAgain');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "\$closure = function(\$foo) { return \$foo; };\n\n        return 'doSomethingAgain';");

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doStaticSomething');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'doStaticSomething';");

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'inline1');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'inline1';");

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'inline2');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'inline2';");

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'inline3');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'inline3';");

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'emptyFunction');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "");
        
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'visibility');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'visibility';");
    }

    public function testInternalMethodContentsReturn()
    {
        $reflectionMethod = new MethodReflection('DOMDocument', 'validate');

        $this->setExpectedException('Zend\Code\Reflection\Exception\InvalidArgumentException');
        $contents = $reflectionMethod->getContents();
    }

    public function testMethodContentsReturnWithoutDocBlock()
    {
        $contents = <<<CONTENTS
    public function doSomething()
    {
        return 'doSomething';
    }
CONTENTS;
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomething');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));

        $contents = '    public function doSomethingElse($one, $two = 2, $three = \'three\') { return \'doSomethingElse\'; }';
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomethingElse');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));

        $contents = <<<'CONTENTS'
    public function doSomethingAgain()
    {
        $closure = function($foo) { return $foo; };

        return 'doSomethingAgain';
    }
CONTENTS;
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomethingAgain');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));

        $contents = '    public function inline1() { return \'inline1\'; }';
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'inline1');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));

        $contents = ' public function inline2() { return \'inline2\'; }';
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'inline2');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));

        $contents = ' public function inline3() { return \'inline3\'; }';
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'inline3');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));
        
        $contents = <<<'CONTENTS'
    public function visibility()
    {
        return 'visibility';
    }
CONTENTS;
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'visibility');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));
    }

    public function testFunctionContentsReturnWithDocBlock()
    {
        $contents = <<<'CONTENTS'
/**
     * Doc block doSomething
     * @return string
     */
    public function doSomething()
    {
        return 'doSomething';
    }
CONTENTS;
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomething');
        $this->assertEquals($contents, $reflectionMethod->getContents(true));
        $this->assertEquals($contents, $reflectionMethod->getContents());

                $contents = <<<'CONTENTS'
/**
     * Awesome doc block
     */
    public function emptyFunction() {}
CONTENTS;
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'emptyFunction');
        $this->assertEquals($contents, $reflectionMethod->getContents(true));
    }
}
