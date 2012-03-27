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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Code\Generator;
use Zend\Code\Generator\FileGenerator,
    Zend\Code\Reflection\FileReflection;

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 * @group Zend_CodeGenerator_Php_File
 */
class FileGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruction()
    {
        $file = new FileGenerator();
        $this->assertEquals('Zend\Code\Generator\FileGenerator', get_class($file));
    }

    public function testSourceContentGetterAndSetter()
    {
        $file = new FileGenerator();
        $file->setSourceContent('Foo');
        $this->assertEquals('Foo', $file->getSourceContent());
    }

    public function testIndentationGetterAndSetter()
    {
        $file = new FileGenerator();
        $file->setIndentation('        ');
        $this->assertEquals('        ', $file->getIndentation());
    }

    public function testToString()
    {
        $codeGenFile = new FileGenerator(array(
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

        $codeGenFile = new FileGenerator(array(
            'class' => array(
                'name' => 'SampleClass'
                )
            ));

        file_put_contents($tempFile, $codeGenFile->generate());

        require_once $tempFile;

        $fileGenerator = FileGenerator::fromReflection(new FileReflection($tempFile));

        unlink($tempFile);

        $this->assertEquals('Zend\Code\Generator\FileGenerator', get_class($fileGenerator));
        $this->assertEquals(1, count($fileGenerator->getClasses()));

    }

    public function testFromFileReflection()
    {
        //$this->markTestSkipped('Must support namespaces');
        $file = __DIR__ . '/TestAsset/TestSampleSingleClass.php';
        require_once $file;

        $codeGenFileFromDisk = FileGenerator::fromReflection(new FileReflection($file));

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
namespace ZendTest\Code\Generator\TestAsset;

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
        $codeGenFile = new FileGenerator(array(
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

    /**
     * @group ZF-11218
     */
    public function testGeneratesUseStatements()
    {
        $file = new FileGenerator();
        $file->setUse('My\Baz')
             ->setUses(array(
                 array('Your\Bar', 'bar'),
             ));
        $generated = $file->generate();
        $this->assertContains('use My\\Baz;', $generated);
        $this->assertContains('use Your\\Bar as bar;', $generated);
    }

    public function testGeneratesNamespaceStatements()
    {
        $file = new FileGenerator();
        $file->setNamespace('Foo\Bar');
        $generated = $file->generate();
        $this->assertContains('namespace Foo\\Bar', $generated, $generated);
    }
}
