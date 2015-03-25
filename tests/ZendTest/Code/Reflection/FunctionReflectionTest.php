<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\FunctionReflection;

/**
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Function
 */
class FunctionReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testParemeterReturn()
    {
        $function = new FunctionReflection('array_splice');
        $parameters = $function->getParameters();
        $this->assertEquals(count($parameters), 4);
        $this->assertInstanceOf('Zend\Code\Reflection\ParameterReflection', array_shift($parameters));
    }

    public function testFunctionDocBlockReturn()
    {
        require_once __DIR__ . '/TestAsset/functions.php';
        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function3');
        $this->assertInstanceOf('Zend\Code\Reflection\DocBlockReflection', $function->getDocBlock());
    }

    public function testGetPrototypeMethod()
    {
        require_once __DIR__ . '/TestAsset/functions.php';

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function2');
        $prototype = array(
            'namespace' => 'ZendTest\Code\Reflection\TestAsset',
            'name' => 'function2',
            'return' => 'string',
            'arguments' => array(
                'one' => array(
                    'type'     => 'string',
                    'required' => true,
                    'by_ref'   => false,
                    'default'  => null,
                ),
                'two' => array(
                    'type'     => 'string',
                    'required' => false,
                    'by_ref'   => false,
                    'default'  => 'two',
                ),
            ),
        );
        $this->assertEquals($prototype, $function->getPrototype());
        $this->assertEquals('string function2(string $one, string $two = \'two\')', $function->getPrototype(FunctionReflection::PROTOTYPE_AS_STRING));
    }

    public function testInternalFunctionBodyReturn()
    {
        $function = new FunctionReflection('array_splice');
        $this->setExpectedException('Zend\Code\Reflection\Exception\InvalidArgumentException');
        $body = $function->getBody();
    }

    public function testFunctionBodyReturn()
    {
        require_once __DIR__ . '/TestAsset/functions.php';

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function1');
        $body = $function->getBody();
        $this->assertEquals("return 'function1';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function4');
        $body = $function->getBody();
        $this->assertEquals("return 'function4';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function5');
        $body = $function->getBody();
        $this->assertEquals("return 'function5';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function6');
        $body = $function->getBody();
        $this->assertEquals("\$closure = function() { return 'bar'; };\n    return 'function6';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function7');
        $body = $function->getBody();
        $this->assertEquals("return 'function7';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function8');
        $body = $function->getBody();
        $this->assertEquals("return 'function8';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function9');
        $body = $function->getBody();
        $this->assertEquals("return 'function9';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function10');
        $body = $function->getBody();
        $this->assertEquals("\$closure = function() { return 'function10'; }; return \$closure();", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function11');
        $body = $function->getBody();
        $this->assertEquals("return 'function11';", trim($body));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function12');
        $body = $function->getBody() ?: '';
        $this->assertEquals('', trim($body));
    }

    public function testFunctionClosureBodyReturn()
    {
        require __DIR__ . '/TestAsset/closures.php';

        $function = new FunctionReflection($function1);
        $body = $function->getBody();
        $this->assertEquals("return 'function1';", trim($body));

        $function = new FunctionReflection($function2);
        $body = $function->getBody();
        $this->assertEquals("return 'function2';", trim($body));

        $function = new FunctionReflection($function3);
        $body = $function->getBody();
        $this->assertEquals("return 'function3';", trim($body));

        $function = new FunctionReflection($function4);
        $body = $function->getBody();
        $this->assertEquals("\$closure = function() { return 'bar'; };\n    return 'function4';", trim($body));

        $function5 = $list1['closure'];
        $function = new FunctionReflection($function5);
        $body = $function->getBody();
        $this->assertEquals("return 'function5';", trim($body));

        $function6 = $list2[0];
        $function = new FunctionReflection($function6);
        $body = $function->getBody();
        $this->assertEquals("return 'function6';", trim($body));

        $function7 = $list3[0];
        $function = new FunctionReflection($function7);
        $body = $function->getBody();
        $this->assertEquals("return \$c = function() { return 'function7'; }; return \$c();", trim($body));

        $function = new FunctionReflection($function8);
        $body = $function->getBody();
        $this->assertEquals("return 'function 8';", trim($body));

        $function = new FunctionReflection($function9);
        $body = $function->getBody() ?: '';
        $this->assertEquals('', trim($body));

        $function = new FunctionReflection($function10);
        $body = $function->getBody();
        $this->assertEquals("return 'function10';", trim($body));
    }

    public function testInternalFunctionContentsReturn()
    {
        $function = new FunctionReflection('array_splice');

        $this->assertEmpty($function->getContents());
    }

    public function testFunctionContentsReturnWithoutDocBlock()
    {
        require_once __DIR__ . '/TestAsset/functions.php';

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function1');
        $content = $function->getContents(false);
        $this->assertEquals("function function1()\n{\n    return 'function1';\n}", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function4');
        $content = $function->getContents(false);
        $this->assertEquals("function function4(\$arg) {\n    return 'function4';\n}", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function5');
        $content = $function->getContents(false);
        $this->assertEquals("function function5() { return 'function5'; }", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function6');
        $content = $function->getContents(false);
        $this->assertEquals("function function6()\n{\n    \$closure = function() { return 'bar'; };\n    return 'function6';\n}", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function7');
        $content = $function->getContents(false);
        $this->assertEquals("function function7() { return 'function7'; }", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function8');
        $content = $function->getContents(false);
        $this->assertEquals("function function8() { return 'function8'; }", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function9');
        $content = $function->getContents(false);
        $this->assertEquals("function function9() { return 'function9'; }", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function10');
        $content = $function->getContents(false);
        $this->assertEquals("function function10() { \$closure = function() { return 'function10'; }; return \$closure(); }", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function11');
        $content = $function->getContents(false);
        $this->assertEquals("function function11() { return 'function11'; }", trim($content));

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function12');
        $content = $function->getContents(false);
        $this->assertEquals("function function12() {}", trim($content));
    }

    /**
     * @group fail
     */
    public function testFunctionClosureContentsReturnWithoutDocBlock()
    {
        require __DIR__ . '/TestAsset/closures.php';

        $function = new FunctionReflection($function2);
        $content = $function->getContents(false);
        $this->assertEquals("function() { return 'function2'; }", trim($content));

        $function = new FunctionReflection($function9);
        $content = $function->getContents(false);
        $this->assertEquals("function() {}", trim($content));

        $function = new FunctionReflection($function10);
        $content = $function->getContents(false);
        $this->assertEquals("function() { return 'function10'; }", trim($content));
    }

    public function testFunctionContentsReturnWithDocBlock()
    {
        require_once __DIR__ . '/TestAsset/functions.php';

        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function3');
        $content = $function->getContents();
        $this->assertEquals("/**\n * Enter description here...\n *\n * @param string \$one\n * @param int \$two"
                          . "\n * @return true\n */\nfunction function3(\$one, \$two = 2)\n{\n    return true;\n}", trim($content));
    }

    public function testFunctionClosureContentsReturnWithDocBlock()
    {
        require __DIR__ . '/TestAsset/closures.php';

        $function = new FunctionReflection($function9);
        $content = $function->getContents();
        $this->assertEquals("/**\n * closure doc block\n */\nfunction() {}", trim($content));
    }

    public function testGetContentsReturnsEmptyContentsOnEvaldCode()
    {
        $functionName = uniqid('generatedFunction');

        eval('name' . 'space ' . __NAMESPACE__ . '; ' . 'fun' . 'ction ' . $functionName . '()' . '{}');

        $reflectionFunction = new FunctionReflection(__NAMESPACE__ . '\\' . $functionName);

        $this->assertSame('', $reflectionFunction->getContents());
    }

    public function testGetContentsReturnsEmptyContentsOnInternalCode()
    {
        $reflectionFunction = new FunctionReflection('max');

        $this->assertSame('', $reflectionFunction->getContents());
    }
}
