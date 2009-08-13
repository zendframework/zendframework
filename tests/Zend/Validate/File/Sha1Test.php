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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

// Call Zend_Validate_File_Sha1Test::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_Sha1Test::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_Sha1
 */
require_once 'Zend/Validate/File/Sha1.php';

/**
 * Sha1 testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_File_Sha1Test extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_Sha1Test");
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
            array('b2a5334847b4328e7d19d9b41fd874dffa911c98', true),
            array('52a5334847b4328e7d19d9b41fd874dffa911c98', false),
            array(array('42a5334847b4328e7d19d9b41fd874dffa911c98', 'b2a5334847b4328e7d19d9b41fd874dffa911c98'), true),
            array(array('42a5334847b4328e7d19d9b41fd874dffa911c98', '72a5334847b4328e7d19d9b41fd874dffa911c98'), false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_Sha1($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(dirname(__FILE__) . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new Zend_Validate_File_Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileSha1NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'test1',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => 'tmp_test1',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileSha1NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertTrue($validator->isValid(dirname(__FILE__) . '/_files/picture.jpg', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new Zend_Validate_File_Sha1('42a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(dirname(__FILE__) . '/_files/picture.jpg', $files));
        $this->assertTrue(array_key_exists('fileSha1DoesNotMatch', $validator->getMessages()));
    }

    /**
     * Ensures that getSha1() returns expected value
     *
     * @return void
     */
    public function testgetSha1()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $this->assertEquals(array('12345' => 'sha1'), $validator->getSha1());

        $validator = new Zend_Validate_File_Sha1(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'sha1', '12333' => 'sha1', '12344' => 'sha1'), $validator->getSha1());
    }

    /**
     * Ensures that getHash() returns expected value
     *
     * @return void
     */
    public function testgetHash()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $this->assertEquals(array('12345' => 'sha1'), $validator->getHash());

        $validator = new Zend_Validate_File_Sha1(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'sha1', '12333' => 'sha1', '12344' => 'sha1'), $validator->getHash());
    }

    /**
     * Ensures that setSha1() returns expected value
     *
     * @return void
     */
    public function testSetSha1()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->setSha1('12333');
        $this->assertEquals(array('12333' => 'sha1'), $validator->getSha1());
        
        $validator->setSha1(array('12321', '12121'));
        $this->assertEquals(array('12321' => 'sha1', '12121' => 'sha1'), $validator->getSha1());
    }

    /**
     * Ensures that setHash() returns expected value
     *
     * @return void
     */
    public function testSetHash()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->setHash('12333');
        $this->assertEquals(array('12333' => 'sha1'), $validator->getSha1());
        
        $validator->setHash(array('12321', '12121'));
        $this->assertEquals(array('12321' => 'sha1', '12121' => 'sha1'), $validator->getSha1());
    }

    /**
     * Ensures that addSha1() returns expected value
     *
     * @return void
     */
    public function testAddSha1()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->addSha1('12344');
        $this->assertEquals(array('12345' => 'sha1', '12344' => 'sha1'), $validator->getSha1());

        $validator->addSha1(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'sha1', '12344' => 'sha1', '12321' => 'sha1', '12121' => 'sha1'), $validator->getSha1());
    }

    /**
     * Ensures that addHash() returns expected value
     *
     * @return void
     */
    public function testAddHash()
    {
        $validator = new Zend_Validate_File_Sha1('12345');
        $validator->addHash('12344');
        $this->assertEquals(array('12345' => 'sha1', '12344' => 'sha1'), $validator->getSha1());

        $validator->addHash(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'sha1', '12344' => 'sha1', '12321' => 'sha1', '12121' => 'sha1'), $validator->getSha1());
    }
}

// Call Zend_Validate_File_Sha1Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_Sha1Test::main") {
    Zend_Validate_File_Sha1Test::main();
}
