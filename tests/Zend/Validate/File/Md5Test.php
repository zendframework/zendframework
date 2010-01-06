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

// Call Zend_Validate_File_Md5Test::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_Md5Test::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_Md5
 */
require_once 'Zend/Validate/File/Md5.php';

/**
 * Md5 testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_File_Md5Test extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_Md5Test");
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
            array('ed74c22109fe9f110579f77b053b8bc3', true),
            array('4d74c22109fe9f110579f77b053b8bc3', false),
            array(array('4d74c22109fe9f110579f77b053b8bc3', 'ed74c22109fe9f110579f77b053b8bc3'), true),
            array(array('4d74c22109fe9f110579f77b053b8bc3', '7d74c22109fe9f110579f77b053b8bc3'), false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Md5($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(dirname(__FILE__) . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_Md5('ed74c22109fe9f110579f77b053b8bc3');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileMd5NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'test1',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => 'tmp_test1',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Md5('ed74c22109fe9f110579f77b053b8bc3');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileMd5NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Md5('ed74c22109fe9f110579f77b053b8bc3');
        $this->assertTrue($validator->isValid(dirname(__FILE__) . '/_files/picture.jpg', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Md5('7d74c22109fe9f110579f77b053b8bc3');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/picture.jpg', $files));
        $this->assertTrue(array_key_exists('fileMd5DoesNotMatch', $validator->getMessages()));
    }

    /**
     * Ensures that getMd5() returns expected value
     *
     * @return void
     */
    public function testgetMd5()
    {
        $validator = new Zend_Validate_File_Md5('12345');
        $this->assertEquals(array('12345' => 'md5'), $validator->getMd5());

        $validator = new Zend_Validate_File_Md5(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'md5', '12333' => 'md5', '12344' => 'md5'), $validator->getMd5());
    }

    /**
     * Ensures that getHash() returns expected value
     *
     * @return void
     */
    public function testgetHash()
    {
        $validator = new Zend_Validate_File_Md5('12345');
        $this->assertEquals(array('12345' => 'md5'), $validator->getHash());

        $validator = new Zend_Validate_File_Md5(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'md5', '12333' => 'md5', '12344' => 'md5'), $validator->getHash());
    }

    /**
     * Ensures that setMd5() returns expected value
     *
     * @return void
     */
    public function testSetMd5()
    {
        $validator = new Zend_Validate_File_Md5('12345');
        $validator->setMd5('12333');
        $this->assertEquals(array('12333' => 'md5'), $validator->getMd5());

        $validator->setMd5(array('12321', '12121'));
        $this->assertEquals(array('12321' => 'md5', '12121' => 'md5'), $validator->getMd5());
    }

    /**
     * Ensures that setHash() returns expected value
     *
     * @return void
     */
    public function testSetHash()
    {
        $validator = new Zend_Validate_File_Md5('12345');
        $validator->setHash('12333');
        $this->assertEquals(array('12333' => 'md5'), $validator->getMd5());

        $validator->setHash(array('12321', '12121'));
        $this->assertEquals(array('12321' => 'md5', '12121' => 'md5'), $validator->getMd5());
    }

    /**
     * Ensures that addMd5() returns expected value
     *
     * @return void
     */
    public function testAddMd5()
    {
        $validator = new Zend_Validate_File_Md5('12345');
        $validator->addMd5('12344');
        $this->assertEquals(array('12345' => 'md5', '12344' => 'md5'), $validator->getMd5());

        $validator->addMd5(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'md5', '12344' => 'md5', '12321' => 'md5', '12121' => 'md5'), $validator->getMd5());
    }

    /**
     * Ensures that addHash() returns expected value
     *
     * @return void
     */
    public function testAddHash()
    {
        $validator = new Zend_Validate_File_Md5('12345');
        $validator->addHash('12344');
        $this->assertEquals(array('12345' => 'md5', '12344' => 'md5'), $validator->getMd5());

        $validator->addHash(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'md5', '12344' => 'md5', '12321' => 'md5', '12121' => 'md5'), $validator->getMd5());
    }
}

// Call Zend_Validate_File_Md5Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_Md5Test::main") {
    Zend_Validate_File_Md5Test::main();
}
