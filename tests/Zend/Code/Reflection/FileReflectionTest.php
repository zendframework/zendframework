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

/**
 * @namespace
 */
namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\FileReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_File
 */
class FileReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testFileConstructor()
    {
        require_once 'Zend/Version.php';
        $reflectionFile = new FileReflection('Zend/Version.php');
        $this->assertEquals(get_class($reflectionFile), 'Zend\Code\Reflection\FileReflection');
    }

    public function testFileConstructorThrowsExceptionOnNonExistentFile()
    {
        $nonExistentFile = 'Non/Existent/File.php';
        $this->setExpectedException('Zend\Code\Reflection\Exception\RuntimeException', 'File Non/Existent/File.php must be required before it can be reflected');
        $reflectionFile = new FileReflection($nonExistentFile);
    }

    public function testFileGetClassReturnsClassReflectionObject()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);
        $this->assertEquals(get_class($reflectionFile), 'Zend\Code\Reflection\FileReflection');
        $this->assertEquals(count($reflectionFile->getClasses()), 1);
        //$this->assertEquals(get_class($reflectionFile->getClass('ZendTest\Code\Reflection\TestAsset\TestSampleClass')), 'Zend\Code\Reflection\ReflectionClass');
    }

    public function testFileGetClassReturnsFirstClassWithNoOptions()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleClass', $reflectionFile->getClass()->getName());
    }


    public function testFileGetClassThrowsExceptionOnNonExistentClassName()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);
        $nonExistentClass = 'Some_Non_Existent_Class';
        
        $this->setExpectedException('Zend\Code\Reflection\Exception\InvalidArgumentException', 'Class by name Some_Non_Existent_Class not found');
        $reflectionFile->getClass($nonExistentClass);
    }

    public function testFileReflectorRequiredFunctionsDoNothing()
    {
        $this->assertNull(FileReflection::export());

        require_once 'Zend/Version.php';
        $reflectionFile = new FileReflection('Zend/Version.php');
        $this->assertEquals('', $reflectionFile->__toString());
    }

    public function testFileGetFilenameReturnsCorrectFilename()
    {
        require_once 'Zend/Version.php';
        $reflectionFile = new FileReflection('Zend/Version.php');

        // Make sure this test works on all platforms
        $this->assertRegExp('#^.*Zend.Version.php$#i', $reflectionFile->getFileName());
    }

    public function testFileGetLineNumbersWorks()
    {
        $this->markTestIncomplete('Line numbering not implemented yet');

        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);
        $this->assertEquals(9, $reflectionFile->getStartLine());
        $this->assertEquals(24, $reflectionFile->getEndLine());
    }

    public function testFileGetDocblockReturnsFileDocblock()
    {
        $this->markTestIncomplete('File docblocks not implemented yet');

        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);
        $this->assertTrue($reflectionFile->getDocblock() instanceof \Zend\Code\Reflection\DocBlockReflection);
    }

    public function testFileGetFunctionsReturnsFunctions()
    {
        $this->markTestIncomplete('Function scanning not implemented yet');

        //$this->markTestSkipped('Regex in Zend_Reflection_File needs work in the function department');
        $fileToRequire = __DIR__ . '/TestAsset/FileOfFunctions.php';
        include_once $fileToRequire;
        $reflectionFile = new FileReflection($fileToRequire);
        $funcs = $reflectionFile->getFunctions();
        $this->assertTrue(current($funcs) instanceof \Zend\Code\Reflection\FunctionReflection);
    }

    public function testFileCanReflectFileWithInterface()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleInterface.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);
        $class = $reflectionFile->getClass();
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleInterface', $class->getName());
        $this->assertTrue($class->isInterface());
    }
    
    public function testFileCanReflectFileWithUses()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass8.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);
        $expected = array(
            array('use' => 'Zend\Config', 'as' => 'ZendConfig'),
            array('use' => 'FooBar\Foo\Bar', 'as' => null),
            array('use' => 'One\Two\Three\Four\Five', 'as' => 'ottff')
            );
        $this->assertSame($expected, $reflectionFile->getUses());
    }
}

