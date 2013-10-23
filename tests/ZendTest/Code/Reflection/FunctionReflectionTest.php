<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
        $function = new FunctionReflection('ZendTest\Code\Reflection\TestAsset\function6');
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
}
