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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @namespace
 */
namespace ZendTest\CodeGenerator\Php;
use Zend\CodeGenerator\Php;
use Zend\Reflection;

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 * @group Zend_CodeGenerator_Php_File
 */
class PhpFileTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruction()
    {
        $file = new Php\PhpFile();
        $this->assertEquals(get_class($file), 'Zend\CodeGenerator\Php\PhpFile');
    }

    public function testSourceContentGetterAndSetter()
    {
        $file = new Php\PhpFile();
        $file->setSourceContent('Foo');
        $this->assertEquals('Foo', $file->getSourceContent());
    }

    public function testIndentationGetterAndSetter()
    {
        $file = new Php\PhpFile();
        $file->setIndentation('        ');
        $this->assertEquals('        ', $file->getIndentation());
    }

    public function testToString()
    {
        $codeGenFile = new Php\PhpFile(array(
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

        $codeGenFile = new Php\PhpFile(array(
            'class' => array(
                'name' => 'SampleClass'
                )
            ));

        file_put_contents($tempFile, $codeGenFile->generate());

        require_once $tempFile;

        $codeGenFileFromDisk = Php\PhpFile::fromReflection(new Reflection\ReflectionFile($tempFile));

        unlink($tempFile);

        $this->assertEquals(get_class($codeGenFileFromDisk), 'Zend\CodeGenerator\Php\PhpFile');
        $this->assertEquals(count($codeGenFileFromDisk->getClasses()), 1);

    }

    public function testFromReflectionFile()
    {
        //$this->markTestSkipped('Must support namespaces');
        $file = __DIR__ . '/TestAsset/TestSampleSingleClass.php';
        require_once $file;

        $codeGenFileFromDisk = Php\PhpFile::fromReflection(new Reflection\ReflectionFile($file));

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
 * @namespace
 */
namespace ZendTest\CodeGenerator\Php\TestAsset;

/**
 * class docblock
 * 
 * @package Zend_Reflection_TestSampleSingleClass
 * 
 */
class TestSampleSingleClass
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
        $codeGenFile = new Php\PhpFile(array(
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
