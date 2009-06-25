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
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see TestHelper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** requires here */
require_once 'Zend/CodeGenerator/Php/File.php';
require_once 'Zend/Reflection/File.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_CodeGenerator_Php
 * @group Zend_CodeGenerator_Php_File
 */
class Zend_CodeGenerator_Php_FileTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstruction()
    {
        $file = new Zend_CodeGenerator_Php_File();
        $this->assertEquals(get_class($file), 'Zend_CodeGenerator_Php_File');
    }
    
    public function testSourceContentGetterAndSetter()
    {
        $file = new Zend_CodeGenerator_Php_File();
        $file->setSourceContent('Foo');
        $this->assertEquals('Foo', $file->getSourceContent());
    }
    
    public function testIndentationGetterAndSetter()
    {
        $file = new Zend_CodeGenerator_Php_File();
        $file->setIndentation('        ');
        $this->assertEquals('        ', $file->getIndentation());
    }
    
    public function testToString()
    {
        $codeGenFile = new Zend_CodeGenerator_Php_File(array(
            'requiredFiles' => array('SampleClass.php'),
            'class' => array(
                'abstract' => true,
                'name' => 'SampleClass',
                'extendedClass' => 'ExtendedClassName',
                'implementedInterfaces' => array('Iterator', 'Traversable')            
                )
            ));

            
        $expectedOutput = <<<EOS
<?php

require_once 'SampleClass.php';

abstract class SampleClass extends ExtendedClassName implements Iterator, Traversable
{


}


EOS;

        $output = $codeGenFile->generate();
        $this->assertEquals($expectedOutput, $output, $output);
    }
    
    public function testFromReflection()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'UnitFile');
        
        $codeGenFile = new Zend_CodeGenerator_Php_File(array(
            'class' => array(
                'name' => 'SampleClass'            
                )
            ));
        
        file_put_contents($tempFile, $codeGenFile->generate());

        require_once $tempFile;

        $codeGenFileFromDisk = Zend_CodeGenerator_Php_File::fromReflection(new Zend_Reflection_File($tempFile));
        
        unlink($tempFile);
        
        $this->assertEquals(get_class($codeGenFileFromDisk), 'Zend_CodeGenerator_Php_File');
        $this->assertEquals(count($codeGenFileFromDisk->getClasses()), 1);
        
    }
    
    public function testFromReflectionFile()
    {
        ///$this->markTestSkipped('skipme');
        $file = dirname(__FILE__) . '/_files/TestSampleSingleClass.php';
        
        require_once $file;
        $codeGenFileFromDisk = Zend_CodeGenerator_Php_File::fromReflection(new Zend_Reflection_File($file));
        
        $codeGenFileFromDisk->getClass()->setMethod(array('name' => 'foobar'));
        
        $expectedOutput = <<<EOS
<?php
/**
 * File header here
 * 
 * @author Ralph Schindler <ralph.schindler@zend.com>
 * 
 */




/**
 * class docblock
 * 
 * @package Zend_Reflection_TestSampleSingleClass
 * 
 */
class Zend_Reflection_TestSampleSingleClass
{

    /**
     * Enter description here...
     * 
     * @return bool
     * 
     */
    public function someMethod()
    {
        /* test test */
    }

    public function foobar()
    {
    }


}




EOS;
        
        $this->assertEquals($expectedOutput, $codeGenFileFromDisk->generate());
        
    }
    
    public function testFileLineEndingsAreAlwaysLineFeed()
    {
        $codeGenFile = new Zend_CodeGenerator_Php_File(array(
            'requiredFiles' => array('SampleClass.php'),
            'class' => array(
                'abstract' => true,
                'name' => 'SampleClass',
                'extendedClass' => 'ExtendedClassName',
                'implementedInterfaces' => array('Iterator', 'Traversable')            
                )
            ));
            
        // explode by newline, this would leave CF in place if it were generated
        $lines = explode("\n", $codeGenFile);
        
        $targetLength = strlen('require_once \'SampleClass.php\';');
        $this->assertEquals($targetLength, strlen($lines[2]));
        $this->assertEquals(';', $lines[2]{$targetLength-1});
    }

}
