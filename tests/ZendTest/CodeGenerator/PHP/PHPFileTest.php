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
namespace ZendTest\CodeGenerator\PHP;
use Zend\CodeGenerator\PHP;
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
class FileTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruction()
    {
        $file = new PHP\PHPFile();
        $this->assertEquals(get_class($file), 'Zend\CodeGenerator\PHP\PHPFile');
    }

    public function testSourceContentGetterAndSetter()
    {
        $file = new PHP\PHPFile();
        $file->setSourceContent('Foo');
        $this->assertEquals('Foo', $file->getSourceContent());
    }

    public function testIndentationGetterAndSetter()
    {
        $file = new PHP\PHPFile();
        $file->setIndentation('        ');
        $this->assertEquals('        ', $file->getIndentation());
    }

    public function testToString()
    {
        $codeGenFile = new PHP\PHPFile(array(
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

        $codeGenFile = new PHP\PHPFile(array(
            'class' => array(
                'name' => 'SampleClass'
                )
            ));

        file_put_contents($tempFile, $codeGenFile->generate());

        require_once $tempFile;

        $codeGenFileFromDisk = PHP\PHPFile::fromReflection(new Reflection\ReflectionFile($tempFile));

        unlink($tempFile);

        $this->assertEquals(get_class($codeGenFileFromDisk), 'Zend\CodeGenerator\PHP\PHPFile');
        $this->assertEquals(count($codeGenFileFromDisk->getClasses()), 1);

    }

    public function testFromReflectionFile()
    {
        //$this->markTestSkipped('Must support namespaces');
        $file = __DIR__ . '/TestAsset/TestSampleSingleClass.php';
        require_once $file;

        $codeGenFileFromDisk = PHP\PHPFile::fromReflection(new Reflection\ReflectionFile($file));

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
namespace ZendTest\CodeGenerator\PHP\TestAsset;

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
        $codeGenFile = new PHP\PHPFile(array(
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
