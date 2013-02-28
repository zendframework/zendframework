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
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class Crc32Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile = __DIR__ . '/_files/picture.jpg';
        $pictureTests = array(
            //    Options, isValid Param, Expected value, Expected message
            array('3f8d07e2',                    $testFile, true, ''),
            array('9f8d07e2',                    $testFile, false, 'fileCrc32DoesNotMatch'),
            array(array('9f8d07e2', '3f8d07e2'), $testFile, true, ''),
            array(array('9f8d07e2', '7f8d07e2'), $testFile, false, 'fileCrc32DoesNotMatch'),
        );

        $testFile = __DIR__ . '/_files/nofile.mo';
        $noFileTests = array(
            //    Options, isValid Param, Expected value, message
            array('3f8d07e2', $testFile, false, 'fileCrc32NotFound'),
        );

        $testFile = __DIR__ . '/_files/testsize.mo';
        $sizeFileTests = array(
            //    Options, isValid Param, Expected value, message
            array('ffeb8d5d', $testFile, true,  ''),
            array('9f8d07e2', $testFile, false, 'fileCrc32DoesNotMatch'),
        );

        // Dupe data in File Upload format
        $testData = array_merge($pictureTests, $noFileTests, $sizeFileTests);
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
        $validator = new File\Crc32($options);
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
            $validator = new File\Crc32($options);
            $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
            if (!$expected) {
                $this->assertTrue(array_key_exists($messageKey, $validator->getMessages()));
            }
        }
    }

    /**
     * Ensures that getCrc32() returns expected value
     *
     * @return void
     */
    public function testgetCrc32()
    {
        $validator = new File\Crc32('12345');
        $this->assertEquals(array('12345' => 'crc32'), $validator->getCrc32());

        $validator = new File\Crc32(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'crc32', '12333' => 'crc32', '12344' => 'crc32'), $validator->getCrc32());
    }

    /**
     * Ensures that getHash() returns expected value
     *
     * @return void
     */
    public function testgetHash()
    {
        $validator = new File\Crc32('12345');
        $this->assertEquals(array('12345' => 'crc32'), $validator->getHash());

        $validator = new File\Crc32(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'crc32', '12333' => 'crc32', '12344' => 'crc32'), $validator->getHash());
    }

    /**
     * Ensures that setCrc32() returns expected value
     *
     * @return void
     */
    public function testSetCrc32()
    {
        $validator = new File\Crc32('12345');
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
        $validator = new File\Crc32('12345');
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
        $validator = new File\Crc32('12345');
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
        $validator = new File\Crc32('12345');
        $validator->addHash('12344');
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32'), $validator->getCrc32());

        $validator->addHash(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'crc32', '12344' => 'crc32', '12321' => 'crc32', '12121' => 'crc32'), $validator->getCrc32());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Crc32('3f8d07e2');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileCrc32NotFound', $validator->getMessages()));
        $this->assertContains("does not exist", current($validator->getMessages()));
    }
}
