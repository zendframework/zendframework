<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_File
 */

namespace ZendTest\File;

use Zend\File\ClassFileLocator;

/**
 * Test class for Zend\File\ClassFileLocator
 *
 * @category   Zend
 * @package    Zend_File
 * @subpackage UnitTests
 * @group      Zend_File
 */
class ClassFileLocatorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructorThrowsInvalidArgumentExceptionForInvalidStringDirectory()
    {
        $this->setExpectedException('Zend\File\Exception\InvalidArgumentException');
        $locator = new ClassFileLocator('__foo__');
    }

    public function testConstructorThrowsInvalidArgumentExceptionForNonDirectoryIteratorArgument()
    {
        $iterator = new \ArrayIterator(array());
        $this->setExpectedException('Zend\File\Exception\InvalidArgumentException');
        $locator = new ClassFileLocator($iterator);
    }

    public function testIterationShouldReturnOnlyPhpFiles()
    {
        $locator = new ClassFileLocator(__DIR__);
        foreach ($locator as $file) {
            $this->assertRegexp('/\.php$/', $file->getFilename());
        }
    }

    public function testIterationShouldReturnOnlyPhpFilesContainingClasses()
    {
        $locator = new ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            if (preg_match('/locator-should-skip-this\.php$/', $file->getFilename())) {
                $found = true;
            }
        }
        $this->assertFalse($found, "Found PHP file not containing a class?");
    }

    public function testIterationShouldReturnInterfaces()
    {
        $locator = new ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            if (preg_match('/LocatorShouldFindThis\.php$/', $file->getFilename())) {
                $found = true;
            }
        }
        $this->assertTrue($found, "Locator skipped an interface?");
    }

    public function testIterationShouldInjectNamespaceInFoundItems()
    {
        $locator = new ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            $classes = $file->getClasses();
            foreach ($classes as $class) {
                if (strpos($class, '\\', 1)) {
                    $found = true;
                }
            }
        }
        $this->assertTrue($found);
    }

    public function testIterationShouldInjectClassInFoundItems()
    {
        $locator = new ClassFileLocator(__DIR__);
        $found = false;
        foreach ($locator as $file) {
            $classes = $file->getClasses();
            foreach ($classes as $class) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testIterationShouldFindMultipleClassesInMultipleNamespacesInSinglePhpFile()
    {
        $locator = new ClassFileLocator(__DIR__);
        $foundFirst = false;
        $foundSecond = false;
        $foundThird = false;
        $foundFourth = false;
        foreach ($locator as $file) {
            if (preg_match('/MultipleClassesInMultipleNamespaces\.php$/', $file->getFilename())) {
                $classes = $file->getClasses();
                foreach ($classes as $class) {
                    if ($class === 'ZendTest\File\TestAsset\LocatorShouldFindFirstClass') {
                        $foundFirst = true;
                    }
                    if ($class === 'ZendTest\File\TestAsset\LocatorShouldFindSecondClass') {
                        $foundSecond = true;
                    }
                    if ($class === 'ZendTest\File\TestAsset\SecondTestNamespace\LocatorShouldFindThirdClass') {
                        $foundThird = true;
                    }
                    if ($class === 'ZendTest\File\TestAsset\SecondTestNamespace\LocatorShouldFindFourthClass') {
                        $foundFourth = true;
                    }
                }
            }
        }
        $this->assertTrue($foundFirst);
        $this->assertTrue($foundSecond);
        $this->assertTrue($foundThird);
        $this->assertTrue($foundFourth);
    }
}
