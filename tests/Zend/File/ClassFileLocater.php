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
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\File;

use Zend\File\ClassFileLocater;

/**
 * Test class for Zend\File\ClassFileLocater
 *
 * @category   Zend
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_File
 */
class ClassFileLocaterTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructorThrowsInvalidArgumentExceptionForInvalidStringDirectory()
    {
        $this->setExpectedException('Zend\File\Exception\InvalidArgumentException');
        $locater = new ClassFileLocater('__foo__');
    }

    public function testConstructorThrowsInvalidArgumentExceptionForNonDirectoryIteratorArgument()
    {
        $iterator = new \ArrayIterator(array());
        $this->setExpectedException('Zend\File\Exception\InvalidArgumentException');
        $locater = new ClassFileLocater($iterator);
    }

    public function testIterationShouldReturnOnlyPhpFiles()
    {
        $locater = new ClassFileLocater(__DIR__);
        foreach ($locater as $file) {
            $this->assertRegexp('/\.php$/', $file->getFilename());
        }
    }

    public function testIterationShouldReturnOnlyPhpFilesContainingClasses()
    {
        $locater = new ClassFileLocater(__DIR__);
        $found = false;
        foreach ($locater as $file) {
            if (preg_match('/locater-should-skip-this\.php$/', $file->getFilename())) {
                $found = true;
            }
        }
        $this->assertFalse($found, "Found PHP file not containing a class?");
    }

    public function testIterationShouldReturnInterfaces()
    {
        $locater = new ClassFileLocater(__DIR__);
        $found = false;
        foreach ($locater as $file) {
            if (preg_match('/LocaterShouldFindThis\.php$/', $file->getFilename())) {
                $found = true;
            }
        }
        $this->assertTrue($found, "Locater skipped an interface?");
    }

    public function testIterationShouldInjectNamespaceInFoundItems()
    {
        $locater = new ClassFileLocater(__DIR__);
        $found = false;
        foreach ($locater as $file) {
            $this->assertTrue(isset($file->namespace));
        }
    }

    public function testIterationShouldInjectClassInFoundItems()
    {
        $locater = new ClassFileLocater(__DIR__);
        $found = false;
        foreach ($locater as $file) {
            $this->assertTrue(isset($file->classname));
        }
    }
}
