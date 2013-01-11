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

use Zend\Code\Reflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Parameter
 */
class ParameterReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDeclaringClassReturn()
    {
        $parameter = new Reflection\ParameterReflection(array('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp2'), 0);
        $this->assertInstanceOf('Zend\Code\Reflection\ClassReflection', $parameter->getDeclaringClass());
    }

    public function testClassReturn_NoClassGiven_ReturnsNull()
    {
        $parameter = new Reflection\ParameterReflection(array('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp2'), 'param1');
        $this->assertNull($parameter->getClass());
    }

    public function testClassReturn()
    {
        $parameter = new Reflection\ParameterReflection(array('ZendTest\Code\Reflection\TestAsset\TestSampleClass2', 'getProp2'), 'param2');
        $this->assertInstanceOf('Zend\Code\Reflection\ClassReflection', $parameter->getClass());
    }

    /**
     * @dataProvider paramTypeTestProvider
     */
    public function testTypeReturn($param, $type)
    {
        $parameter = new Reflection\ParameterReflection(array('ZendTest\Code\Reflection\TestAsset\TestSampleClass5', 'doSomething'), $param);
        $this->assertEquals($type, $parameter->getType());
    }

    public function paramTypeTestProvider()
    {
        return array(
            array('one','int'),
            array('two','int'),
            array('three','string'),
            array('array','array'),
            array('class','ZendTest\Code\Reflection\TestAsset\TestSampleClass')
        );
    }
}
