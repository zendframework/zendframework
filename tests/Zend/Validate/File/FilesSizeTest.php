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
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Validate_File_FilesSizeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_FilesSizeTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_FilesSize
 */
require_once 'Zend/Validate/File/FilesSize.php';

/**
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_File_FilesSizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_FilesSizeTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->multipleOptionsDetected = false;
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(array('min' => 0, 'max' => 2000), true, true, false),
            array(array('min' => 0, 'max' => '2 MB'), true, true, true),
            array(array('min' => 0, 'max' => '2MB'), true, true, true),
            array(array('min' => 0, 'max' => '2  MB'), true, true, true),
            array(2000, true, true, false),
            array(array('min' => 0, 'max' => 500), false, false, false),
            array(500, false, false, false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_FilesSize($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[2],
                $validator->isValid(dirname(__FILE__) . '/_files/testsize2.mo'),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[3],
                $validator->isValid(dirname(__FILE__) . '/_files/testsize3.mo'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_FilesSize(array('min' => 0, 'max' => 200));
        $this->assertEquals(false, $validator->isValid(dirname(__FILE__) . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileFilesSizeNotReadable', $validator->getMessages()));

        $validator = new Zend_Validate_File_FilesSize(array('min' => 0, 'max' => 500000));
        $this->assertEquals(true, $validator->isValid(array(
            dirname(__FILE__) . '/_files/testsize.mo',
            dirname(__FILE__) . '/_files/testsize.mo',
            dirname(__FILE__) . '/_files/testsize2.mo')));
        $this->assertEquals(true, $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo'));
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100));
        $this->assertEquals('1B', $validator->getMin());

        try {
            $validator = new Zend_Validate_File_FilesSize(array('min' => 100, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100));
        $this->assertEquals('1B', $validator->getMin());
    }

    /**
     * Ensures that setMin() returns expected value
     *
     * @return void
     */
    public function testSetMin()
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1000, 'max' => 10000));
        $validator->setMin(100);
        $this->assertEquals('100B', $validator->getMin());

        try {
            $validator->setMin(20000);
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("less than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100));
        $this->assertEquals('100B', $validator->getMax());

        try {
            $validator = new Zend_Validate_File_FilesSize(array('min' => 100, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new Zend_Validate_File_FilesSize(array('min' => 1, 'max' => 100000));
        $this->assertEquals('97.66kB', $validator->getMax());

        $validator = new Zend_Validate_File_FilesSize(2000);
        $validator->setUseByteString(false);
        $test = $validator->getMax();
        $this->assertEquals('2000', $test);
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax()
    {
        $validator = new Zend_Validate_File_FilesSize(array('min' => 1000, 'max' => 10000));
        $validator->setMax(1000000);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals('976.56kB', $validator->getMax());
    }

    public function testConstructorShouldRaiseErrorWhenPassedMultipleOptions()
    {
        $handler = set_error_handler(array($this, 'errorHandler'), E_USER_NOTICE);
        $validator = new Zend_Validate_File_FilesSize(1000, 10000);
        restore_error_handler();
// @todo: Preperation for 2.0... needs to be cleared with the dev-team
//        $this->assertTrue($this->multipleOptionsDetected);
    }

    public function errorHandler($errno, $errstr)
    {
        if (strstr($errstr, 'deprecated')) {
            $this->multipleOptionsDetected = true;
        }
    }
}

// Call Zend_Validate_File_FilesSizeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_FilesSizeTest::main") {
    Zend_Validate_File_FilesSizeTest::main();
}
