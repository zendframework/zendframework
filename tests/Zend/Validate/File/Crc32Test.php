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

// Call Zend_Validate_File_Crc32Test::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_Crc32Test::main");
}

/**
 * Test helper
 */

/**
 * @see Zend_Validate_File_Crc32
 */

/**
 * Crc32 testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_File_Crc32Test extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_Crc32Test");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('3f8d07e2', true),
            array('9f8d07e2', false),
            array(array('9f8d07e2', '3f8d07e2'), true),
            array(array('9f8d07e2', '7f8d07e2'), false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Crc32($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(dirname(__FILE__) . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_Crc32('3f8d07e2');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileCrc32NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'test1',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => 'tmp_test1',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Crc32('3f8d07e2');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileCrc32NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Crc32('3f8d07e2');
        $this->assertTrue($validator->isValid(dirname(__FILE__) . '/_files/picture.jpg', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Crc32('9f8d07e2');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/picture.jpg', $files));
        $this->assertTrue(array_key_exists('fileCrc32DoesNotMatch', $validator->getMessages()));
    }

    /**
     * Ensures that getCrc32() returns expected value
     *
     * @return void
     */
    public function testgetCrc32()
    {
        $validator = new Zend_Validate_File_Crc32('12345');
        $this->assertEquals(array('12345' => 'crc32'), $validator->getCrc32());

        $validator = new Zend_Validate_File_Crc32(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'crc32', '12333' => 'crc32', '12344' => 'crc32'), $validator->getCrc32());
    }

    /**
     * Ensures that getHash() returns expected value
     *
     * @return void
     */
    public function testgetHash()
    {
        $validator = new Zend_Validate_File_Crc32('12345');
        $this->assertEquals(array('12345' => 'crc32'), $validator->getHash());

        $validator = new Zend_Validate_File_Crc32(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'crc32', '12333' => 'crc32', '12344' => 'crc32'), $validator->getHash());
    }

    /**
     * Ensures that setCrc32() returns expected value
     *
     * @return void
     */
    public function testSetCrc32()
    {
        $validator = new Zend_Validate_File_Crc32('12345');
        $validator->setCrc32('12333');
        $this->assertEquals(array('12333' => 'crc32'), $validator->getCrc32());

        $validator->setCrc32(array('12321', '12121'));
        $this->assertEquals(array('12321' => 'crc32', '12121' => 'crc32'), $validator->getCrc32());
    }

    /**
     * Ensures that setHash() returns expected value
     *
     * @return void
     */
    public function testSetHash()
    {
        $validator = new Zend_Validate_File_Crc32('12345');
        $validator->setHash('12333');
        $this->assertEquals(array('12333' => 'crc32'), $validator->getCrc32());

        $validator->setHash(array('12321', '12121'));
        $this->assertEquals(array('12321' => 'crc32', '12121' => 'crc32'), $validator->getCrc32());
    }

    /**
     * Ensures that addCrc32() returns expected value
     *
     * @return void
     */
    public function testAddCrc32()
    {
        $validator = new Zend_Validate_File_Crc32('12345');
        $validator->addCrc32('12344');
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32'), $validator->getCrc32());

        $validator->addCrc32(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32', '12321' => 'crc32', '12121' => 'crc32'), $validator->getCrc32());
    }

    /**
     * Ensures that addHash() returns expected value
     *
     * @return void
     */
    public function testAddHash()
    {
        $validator = new Zend_Validate_File_Crc32('12345');
        $validator->addHash('12344');
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32'), $validator->getCrc32());

        $validator->addHash(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32', '12321' => 'crc32', '12121' => 'crc32'), $validator->getCrc32());
    }
}

// Call Zend_Validate_File_Crc32Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_Crc32Test::main") {
    Zend_Validate_File_Crc32Test::main();
}
