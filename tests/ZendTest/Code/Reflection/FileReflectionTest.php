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

use Zend\Code\Reflection\FileReflection;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @group      Zend_Reflection
 * @group      Zend_Reflection_File
 */
class FileReflectionTest extends \PHPUnit_Framework_TestCase
{
    public function testFileConstructor()
    {
        require_once 'Zend/Version/Version.php';
        $reflectionFile = new FileReflection('Zend/Version/Version.php');
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

        require_once 'Zend/Version/Version.php';
        $reflectionFile = new FileReflection('Zend/Version/Version.php');
        $this->assertEquals('', $reflectionFile->__toString());
    }

    public function testFileGetFilenameReturnsCorrectFilename()
    {
        require_once 'Zend/Version/Version.php';
        $reflectionFile = new FileReflection('Zend/Version/Version.php');

        // Make sure this test works on all platforms
        $this->assertRegExp('#^.*Zend.Version.Version.php$#i', $reflectionFile->getFileName());
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

    public function testFileGetDocBlockReturnsFileDocBlock()
    {
        $fileToReflect = __DIR__ . '/TestAsset/TestSampleClass7.php';
        include_once $fileToReflect;
        $reflectionFile = new FileReflection($fileToReflect);

        $reflectionDocBlock = $reflectionFile->getDocBlock();
        $this->assertTrue($reflectionDocBlock instanceof \Zend\Code\Reflection\DocBlockReflection);

        $authorTag = $reflectionDocBlock->getTag('author');
        $this->assertEquals('Jeremiah Small', $authorTag->getAuthorName());
        $this->assertEquals('jsmall@soliantconsulting.com', $authorTag->getAuthorEmail());
    }

    public function testFileGetFunctionsReturnsFunctions()
    {
        $this->markTestIncomplete('Function scanning not implemented yet');

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
