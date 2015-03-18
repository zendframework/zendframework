<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\MethodReflection;
use ZendTest\Code\Reflection\TestAsset\InjectableMethodReflection;

/**
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
        $this->assertEmpty($reflectionMethod->getBody());
    }

    public function testGetBodyReturnsCorrectBody()
    {
        $body = '
        //we need a multi-line method body.
        $assigned = 1;
        $alsoAssigined = 2;
        return \'mixedValue\';';

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass6', 'doSomething');
        $this->assertEquals($body, $reflectionMethod->getBody());

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'doSomething');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'doSomething';");

        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass11',
            'doSomethingElse'
        );
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'doSomethingElse';");

        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass11',
            'doSomethingAgain'
        );
        $body = $reflectionMethod->getBody();
        $this->assertEquals(
            trim($body),
            "\$closure = function(\$foo) { return \$foo; };\n\n        return 'doSomethingAgain';"
        );

        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass11',
            'doStaticSomething'
        );
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

        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass11',
            'emptyFunction'
        );
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "");
        
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'visibility');
        $body = $reflectionMethod->getBody();
        $this->assertEquals(trim($body), "return 'visibility';");
    }

    public function testInternalMethodContentsReturn()
    {
        $reflectionMethod = new MethodReflection('DOMDocument', 'validate');
        $this->assertEquals('', $reflectionMethod->getContents());
    }

    /**
     * @group 6275
     */
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

        $contents = '    public function doSomethingElse($one, $two = 2, $three = \'three\')'
            . ' { return \'doSomethingElse\'; }';
        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass11',
            'doSomethingElse'
        );
        $this->assertEquals($contents, $reflectionMethod->getContents(false));

        $contents = <<<'CONTENTS'
    public function doSomethingAgain()
    {
        $closure = function($foo) { return $foo; };

        return 'doSomethingAgain';
    }
CONTENTS;
        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass11',
            'doSomethingAgain'
        );
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
        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass11',
            'emptyFunction'
        );
        $this->assertEquals($contents, $reflectionMethod->getContents(true));
    }

    public function testGetPrototypeMethod()
    {
        $reflectionMethod = new MethodReflection(
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass10',
            'doSomethingElse'
        );
        $prototype = array(
            'namespace' => 'ZendTest\Code\Reflection\TestAsset',
            'class' => 'TestSampleClass10',
            'name' => 'doSomethingElse',
            'visibility' => 'public',
            'return' => 'int',
            'arguments' => array(
                'one' => array(
                    'type'     => 'int',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
                'two' => array(
                    'type'     => 'int',
                    'required' => false,
                    'by_ref'   => false,
                    'default'  => 2,
                ),
                'three' => array(
                    'type'     => 'string',
                    'required' => false,
                    'by_ref'   => false,
                    'default'  => 'three',
                ),
            ),
        );
        $this->assertEquals($prototype, $reflectionMethod->getPrototype());
        $this->assertEquals(
            'public int doSomethingElse(int $one, int $two = 2, string $three = \'three\')',
            $reflectionMethod->getPrototype(MethodReflection::PROTOTYPE_AS_STRING)
        );

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp2');
        $prototype = array(
            'namespace' => 'ZendTest\Code\Reflection\TestAsset',
            'class' => 'TestSampleClass2',
            'name' => 'getProp2',
            'visibility' => 'public',
            'return' => 'mixed',
            'arguments' => array(
                'param1' => array(
                    'type'     => '',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
                'param2' => array(
                    'type'     => 'ZendTest\Code\Reflection\TestAsset\TestSampleClass',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
            ),
        );
        $this->assertEquals($prototype, $reflectionMethod->getPrototype());
        $this->assertEquals(
            'public mixed getProp2($param1, ZendTest\Code\Reflection\TestAsset\TestSampleClass $param2)',
            $reflectionMethod->getPrototype(MethodReflection::PROTOTYPE_AS_STRING)
        );

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass12', 'doSomething');
        $prototype = array(
            'namespace' => 'ZendTest\Code\Reflection\TestAsset',
            'class' => 'TestSampleClass12',
            'name' => 'doSomething',
            'visibility' => 'protected',
            'return' => 'string',
            'arguments' => array(
                'one' => array(
                    'type'     => 'int',
                    'required' => true,
                    'by_ref'   => true,
                    'default'  => null,
                ),
                'two' => array(
                    'type'     => 'int',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
            ),
        );
        $this->assertEquals($prototype, $reflectionMethod->getPrototype());
        $this->assertEquals(
            'protected string doSomething(int &$one, int $two)',
            $reflectionMethod->getPrototype(MethodReflection::PROTOTYPE_AS_STRING)
        );
    }

    public function testGetAnnotationsWithNoNameInformations()
    {
        $reflectionMethod = new InjectableMethodReflection(
            // TestSampleClass5 has the annotations required to get to the
            // right point in the getAnnotations method.
            'ZendTest\Code\Reflection\TestAsset\TestSampleClass5',
            'doSomething'
        );

        $annotationManager = new \Zend\Code\Annotation\AnnotationManager();

        $fileScanner = $this->getMockBuilder('Zend\Code\Scanner\CachingFileScanner')
                            ->disableOriginalConstructor()
                            ->getMock();

        $reflectionMethod->setFileScanner($fileScanner);

        $fileScanner->expects($this->any())
                    ->method('getClassNameInformation')
                    ->will($this->returnValue(false));

        $this->assertFalse($reflectionMethod->getAnnotations($annotationManager));
    }

    /**
     * @group 5062
     */
    public function testGetContentsWithCoreClass()
    {
        $reflectionMethod = new MethodReflection('DateTime', 'format');
        $this->assertEquals("", $reflectionMethod->getContents(false));
    }

    public function testGetContentsReturnsEmptyContentsOnEvaldCode()
    {
        $className = uniqid('MethodReflectionTestGenerated');

        eval('name' . 'space ' . __NAMESPACE__ . '; cla' . 'ss ' . $className . '{fun' . 'ction foo(){}}');

        $reflectionMethod = new MethodReflection(__NAMESPACE__ . '\\' . $className, 'foo');

        $this->assertSame('', $reflectionMethod->getContents());
        $this->assertSame('', $reflectionMethod->getBody());
    }

    public function testGetContentsReturnsEmptyContentsOnInternalCode()
    {
        $reflectionMethod = new MethodReflection('ReflectionClass', 'getName');
        $this->assertSame('', $reflectionMethod->getContents());
    }

    /**
     * @group 6275
     */
    public function testCodeGetContentsDoesNotThrowExceptionOnDocBlock()
    {

        $contents = <<<'CONTENTS'
    function getCacheKey() {
        $args = func_get_args();
 
        $cacheKey = '';
 
        foreach($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $argElement) {
                    $cacheKey = hash("sha256", $cacheKey.$argElement);
                }
            }
            else {
                $cacheKey = hash("sha256", $cacheKey.$arg);
            }
            //blah
        }
 
        return $cacheKey;
    }
CONTENTS;

        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', 'getCacheKey');
        $this->assertEquals($contents, $reflectionMethod->getContents(false));
    }

    /**
     * @group 6275
     */
    public function testCodeGetBodyReturnsEmptyWithCommentedFunction()
    {
        $this->setExpectedException('ReflectionException');
        $reflectionMethod = new MethodReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass11', '__prototype');
        $reflectionMethod->getBody();
    }

    /**
     * @group 6620
     */
    public function testCanParseClassBodyWhenUsingTrait()
    {
        if (version_compare(PHP_VERSION, '5.4', 'lt')) {
            $this->markTestSkipped('This test requires PHP version 5.4+');
        }

        require_once __DIR__ .'/TestAsset/TestTraitClass1.php';
        require_once __DIR__. '/TestAsset/TestTraitClass2.php';
        // $method = new \Zend\Code\Reflection\ClassReflection('\FooClass');
        // $traits = current($method->getTraits());
        $method = new \Zend\Code\Reflection\MethodReflection('FooClass', 'getDummy');
        $this->assertEquals(trim($method->getBody()), 'return $this->dummy;');
    }
}
