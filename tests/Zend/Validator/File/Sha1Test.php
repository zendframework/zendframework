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
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Validator\File;
use Zend\Validator\File;

/**
 * @see Zend_Validator_File_Sha1
 */

/**
 * Sha1 testbed
 *
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class Sha1Test extends \PHPUnit_Framework_TestCase
{
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
            $validator = new File\Sha1($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new File\Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileSha1NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'test1',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => 'tmp_test1',
            'error'    => 0
        );
        $validator = new File\Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileSha1NotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new File\Sha1('b2a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertTrue($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new File\Sha1('42a5334847b4328e7d19d9b41fd874dffa911c98');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));
        $this->assertTrue(array_key_exists('fileSha1DoesNotMatch', $validator->getMessages()));
    }

    /**
     * Ensures that getSha1() returns expected value
     *
     * @return void
     */
    public function testgetSha1()
    {
        $validator = new File\Sha1('12345');
        $this->assertEquals(array('12345' => 'sha1'), $validator->getSha1());

        $validator = new File\Sha1(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'sha1', '12333' => 'sha1', '12344' => 'sha1'), $validator->getSha1());
    }

    /**
     * Ensures that getHash() returns expected value
     *
     * @return void
     */
    public function testgetHash()
    {
        $validator = new File\Sha1('12345');
        $this->assertEquals(array('12345' => 'sha1'), $validator->getHash());

        $validator = new File\Sha1(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'sha1', '12333' => 'sha1', '12344' => 'sha1'), $validator->getHash());
    }

    /**
     * Ensures that setSha1() returns expected value
     *
     * @return void
     */
    public function testSetSha1()
    {
        $validator = new File\Sha1('12345');
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
        $validator = new File\Sha1('12345');
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
        $validator = new File\Sha1('12345');
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
        $validator = new File\Sha1('12345');
        $validator->addHash('12344');
        $this->assertEquals(array('12345' => 'sha1', '12344' => 'sha1'), $validator->getSha1());

        $validator->addHash(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'sha1', '12344' => 'sha1', '12321' => 'sha1', '12121' => 'sha1'), $validator->getSha1());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Sha1('12345');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileSha1NotFound', $validator->getMessages()));
        $this->assertContains("'nofile.mo'", current($validator->getMessages()));
    }
}
