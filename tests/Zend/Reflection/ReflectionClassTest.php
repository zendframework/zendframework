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
 * @version    $Id $
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
 *
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Class
 */
class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{

    static protected $_sampleClassFileRequired = false;

    public function setup()
    {
//        // ensure we are only required this file once per runtime
//        if (self::$_sampleClassFileRequired === false) {
//            $fileToRequire = __DIR__ . '/_files/TestSampleClass.php';
//            self::$_sampleClassFileRequired = true;
//        }
    }

    public function testMethodReturns()
    {

        $reflectionClass = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass2');

        $methodByName = $reflectionClass->getMethod('getProp1');
        $this->assertEquals('Zend\Reflection\ReflectionMethod', get_class($methodByName));

        $methodsAll = $reflectionClass->getMethods();
        $this->assertEquals(3, count($methodsAll));

        $firstMethod = array_shift($methodsAll);
        $this->assertEquals('getProp1', $firstMethod->getName());
    }

    public function testPropertyReturns()
    {
        $reflectionClass = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass2');

        $propertyByName = $reflectionClass->getProperty('_prop1');
        $this->assertEquals('Zend\Reflection\ReflectionProperty', get_class($propertyByName));

        $propertiesAll = $reflectionClass->getProperties();
        $this->assertEquals(2, count($propertiesAll));

        $firstProperty = array_shift($propertiesAll);
        $this->assertEquals('_prop1', $firstProperty->getName());
    }

    public function testParentReturn()
    {
        $reflectionClass = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass');

        $parent = $reflectionClass->getParentClass();
        $this->assertEquals('Zend\Reflection\ReflectionClass', get_class($parent));
        $this->assertEquals('ArrayObject', $parent->getName());

    }

    public function testInterfaceReturn()
    {
        $reflectionClass = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass4');

        $interfaces = $reflectionClass->getInterfaces();
        $this->assertEquals(1, count($interfaces));

        $interface = array_shift($interfaces);
        $this->assertEquals('ZendTest\Reflection\TestAsset\TestSampleClassInterface', $interface->getName());

    }

    public function testGetContentsReturnsContents()
    {
        $reflectionClass = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass2');
        $target = <<<EOS
{

    protected \$_prop1 = null;
    protected \$_prop2 = null;

    public function getProp1()
    {
        return \$this->_prop1;
    }

    public function getProp2(\$param1, TestSampleClass \$param2)
    {
        return \$this->_prop2;
    }

    public function getIterator()
    {
        return array();
    }

}
EOS;
        $this->assertEquals($target, $reflectionClass->getContents());
    }

    public function testStartLine()
    {
        $reflectionClass = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass5');

        $this->assertEquals(16, $reflectionClass->getStartLine());
        $this->assertEquals(5, $reflectionClass->getStartLine(true));
    }


    public function testGetDeclaringFileReturnsFilename()
    {
        $reflectionClass = new Reflection\ReflectionClass('\ZendTest\Reflection\TestAsset\TestSampleClass2');
        $this->assertContains('TestSampleClass2.php', $reflectionClass->getDeclaringFile()->getFileName());
    }

}
