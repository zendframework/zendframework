<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File;

/**
 * Sha1 testbed
 *
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class Sha1Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile   = __DIR__ . '/_files/picture.jpg';
        $pictureTests = array(
            //    Options, isValid Param, Expected value, Expected message
            array('b2a5334847b4328e7d19d9b41fd874dffa911c98', $testFile, true,  ''),
            array('52a5334847b4328e7d19d9b41fd874dffa911c98', $testFile, false, 'fileSha1DoesNotMatch'),
            array(
                array('42a5334847b4328e7d19d9b41fd874dffa911c98', 'b2a5334847b4328e7d19d9b41fd874dffa911c98'),
                $testFile, true, ''
            ),
            array(
                array('42a5334847b4328e7d19d9b41fd874dffa911c98', '72a5334847b4328e7d19d9b41fd874dffa911c98'),
                $testFile, false, 'fileSha1DoesNotMatch'
            ),
        );

        $testFile   = __DIR__ . '/_files/nofile.mo';
        $noFileTests = array(
            //    Options, isValid Param, Expected value, message
            array('b2a5334847b4328e7d19d9b41fd874dffa911c98', $testFile, false, 'fileSha1NotFound'),
        );

        // Dupe data in File Upload format
        $testData = array_merge($pictureTests, $noFileTests);
        foreach ($testData as $data) {
            $fileUpload = array(
                'tmp_name' => $data[1], 'name' => basename($data[1]),
                'size' => 200, 'error' => 0, 'type' => 'text'
            );
            $testData[] = array($data[0], $fileUpload, $data[2], $data[3]);
        }
        return $testData;
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testBasic($options, $isValidParam, $expected, $messageKey)
    {
        $validator = new File\Sha1($options);
        $this->assertEquals($expected, $validator->isValid($isValidParam));
        if (!$expected) {
            $this->assertTrue(array_key_exists($messageKey, $validator->getMessages()));
        }
    }

    /**
     * Ensures that the validator follows expected behavior for legacy Zend\Transfer API
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testLegacy($options, $isValidParam, $expected, $messageKey)
    {
        if (is_array($isValidParam)) {
            $validator = new File\Sha1($options);
            $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
            if (!$expected) {
                $this->assertTrue(array_key_exists($messageKey, $validator->getMessages()));
            }
        }
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
        $this->assertContains("does not exist", current($validator->getMessages()));
    }
}
