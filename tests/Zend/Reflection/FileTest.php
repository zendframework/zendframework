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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Reflection_File
 */
require_once 'Zend/Reflection/File.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_File
 */
class Zend_Reflection_FileTest extends PHPUnit_Framework_TestCase
{

    public function testFileConstructor()
    {
        require_once 'Zend/Version.php';
        $reflectionFile = new Zend_Reflection_File('Zend/Version.php');
        $this->assertEquals(get_class($reflectionFile), 'Zend_Reflection_File');
    }
    
    /**
     * @expectedException Zend_Reflection_Exception
     */
    public function testFileConstructorThrowsExceptionOnNonExistentFile()
    {
        $nonExistentFile = 'Non/Existent/File.php';
        $reflectionFile = new Zend_Reflection_File($nonExistentFile);
        $this->fail('Exception should have been thrown');        
    }
    
    public function testFileGetClassReturnsClassReflectionObject()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
        require_once $fileToRequire;
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        $this->assertEquals(get_class($reflectionFile), 'Zend_Reflection_File');
        $this->assertEquals(count($reflectionFile->getClasses()), 6);
        $this->assertEquals(get_class($reflectionFile->getClass('Zend_Reflection_TestSampleClass2')), 'Zend_Reflection_Class');
    }
    
    public function testFileGetClassReturnsFirstClassWithNoOptions()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
        require_once $fileToRequire;
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        $this->assertEquals('Zend_Reflection_TestSampleClass', $reflectionFile->getClass()->getName());
    }
    
    
    /**
     * @expectedException Zend_Reflection_Exception
     */
    public function testFileGetClassThrowsExceptionOnNonExistentClassName()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
        require_once $fileToRequire;
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        $nonExistentClass = 'Some_Non_Existent_Class';
        $reflectionFile->getClass($nonExistentClass);
        $this->fail('Exception should have been thrown');
    }
    
    public function testFileReflectorRequiredFunctionsDoNothing()
    {
        $this->assertNull(Zend_Reflection_File::export());
        
        require_once 'Zend/Version.php';
        $reflectionFile = new Zend_Reflection_File('Zend/Version.php');
        $this->assertEquals('', $reflectionFile->__toString());
    }
    
    public function testFileGetFilenameReturnsCorrectFilename()
    {
        require_once 'Zend/Version.php';
        $reflectionFile = new Zend_Reflection_File('Zend/Version.php');
        
        // Make sure this test works on all platforms
        $this->assertRegExp('#^.*Zend.Version.php$#i', $reflectionFile->getFileName());
    }
    
    public function testFileGetLineNumbersWorks()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
        require_once $fileToRequire;
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        $this->assertEquals(9, $reflectionFile->getStartLine());
        $this->assertEquals(138, $reflectionFile->getEndLine());
    }
    
    public function testFileGetDocblockReturnsFileDocblock()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
        require_once $fileToRequire;
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        $this->assertTrue($reflectionFile->getDocblock() instanceof Zend_Reflection_Docblock);
    }
    
    public function testFileGetFunctionsReturnsFunctions()
    {
        $this->markTestSkipped('Regex in Zend_Reflection_File needs work in the function department');
        $fileToRequire = dirname(__FILE__) . '/_files/FileOfFunctions.php';
        require_once $fileToRequire;
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        echo count($reflectionFile->getFunctions());
    }
    
    public function testFileCanReflectFileWithInterface()
    {
        $fileToRequire = dirname(__FILE__) . '/_files/TestSampleInterface.php';
        require_once $fileToRequire; 
        $reflectionFile = new Zend_Reflection_File($fileToRequire);
        $class = $reflectionFile->getClass();
        $this->assertEquals('Zend_Reflection_TestSampleInterface', $class->getName());
        $this->assertTrue($class->isInterface());
    }
}

