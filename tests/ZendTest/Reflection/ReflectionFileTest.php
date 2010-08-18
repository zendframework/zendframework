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
 * @group      Zend_Reflection_File
 */
class ReflectionFileTest extends \PHPUnit_Framework_TestCase
{
    public function testFileConstructor()
    {
        require_once 'Zend/Version.php';
        $reflectionFile = new Reflection\ReflectionFile('Zend/Version.php');
        $this->assertEquals(get_class($reflectionFile), 'Zend\Reflection\ReflectionFile');
    }

    /**
     * @expectedException Zend\Reflection\Exception
     */
    public function testFileConstructorThrowsExceptionOnNonExistentFile()
    {
        $nonExistentFile = 'Non/Existent/File.php';
        $reflectionFile = new Reflection\ReflectionFile($nonExistentFile);
        $this->fail('Exception should have been thrown');
    }

    public function testFileGetClassReturnsClassReflectionObject()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new Reflection\ReflectionFile($fileToReflect);
        $this->assertEquals(get_class($reflectionFile), 'Zend\Reflection\ReflectionFile');
        $this->assertEquals(count($reflectionFile->getClasses()), 1);
        //$this->assertEquals(get_class($reflectionFile->getClass('ZendTest\Reflection\TestAsset\TestSampleClass')), 'Zend\Reflection\ReflectionClass');
    }

    public function testFileGetClassReturnsFirstClassWithNoOptions()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new Reflection\ReflectionFile($fileToReflect);
        $this->assertEquals('ZendTest\Reflection\TestAsset\TestSampleClass', $reflectionFile->getClass()->getName());
    }


    /**
     * @expectedException Zend\Reflection\Exception
     */
    public function testFileGetClassThrowsExceptionOnNonExistentClassName()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new Reflection\ReflectionFile($fileToReflect);
        $nonExistentClass = 'Some_Non_Existent_Class';
        $reflectionFile->getClass($nonExistentClass);
        $this->fail('Exception should have been thrown');
    }

    public function testFileReflectorRequiredFunctionsDoNothing()
    {
        $this->assertNull(Reflection\ReflectionFile::export());

        require_once 'Zend/Version.php';
        $reflectionFile = new Reflection\ReflectionFile('Zend/Version.php');
        $this->assertEquals('', $reflectionFile->__toString());
    }

    public function testFileGetFilenameReturnsCorrectFilename()
    {
        require_once 'Zend/Version.php';
        $reflectionFile = new Reflection\ReflectionFile('Zend/Version.php');

        // Make sure this test works on all platforms
        $this->assertRegExp('#^.*Zend.Version.php$#i', $reflectionFile->getFileName());
    }

    public function testFileGetLineNumbersWorks()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new Reflection\ReflectionFile($fileToReflect);
        $this->assertEquals(9, $reflectionFile->getStartLine());
        $this->assertEquals(24, $reflectionFile->getEndLine());
    }

    public function testFileGetDocblockReturnsFileDocblock()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new Reflection\ReflectionFile($fileToReflect);
        $this->assertTrue($reflectionFile->getDocblock() instanceof \Zend\Reflection\ReflectionDocblock);
    }

    public function testFileGetFunctionsReturnsFunctions()
    {
        $this->markTestSkipped('Regex in Zend_Reflection_File needs work in the function department');
        $fileToRequire = __DIR__ . '/_files/FileOfFunctions.php';
        include_once $fileToRequire;
        $reflectionFile = new Reflection\ReflectionFile($fileToRequire);
        echo count($reflectionFile->getFunctions());
    }

    public function testFileCanReflectFileWithInterface()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleInterface.php';
        include_once $fileToReflect;
        $reflectionFile = new Reflection\ReflectionFile($fileToReflect);
        $class = $reflectionFile->getClass();
        $this->assertEquals('ZendTest\Reflection\TestAsset\TestSampleInterface', $class->getName());
        $this->assertTrue($class->isInterface());
    }
    
    public function testFileCanReflectFileWithUses()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass8.php';
        include_once $fileToReflect;
        $reflectionFile = new Reflection\ReflectionFile($fileToReflect);
        $expected = array(
            array('namespace' => 'Zend\Config', 'as' => 'ZendConfig', 'asResolved' => 'ZendConfig'),
            array('namespace' => 'FooBar\Foo\Bar', 'as' => '', 'asResolved' => 'Bar'),
            array('namespace' => 'One\Two\Three\Four\Five', 'as' => 'ottff', 'asResolved' => 'ottff')
            );
        $this->assertSame($expected, $reflectionFile->getUses());
    }
}

