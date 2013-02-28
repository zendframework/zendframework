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
 * Md5 testbed
 *
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class Md5Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile = __DIR__ . '/_files/picture.jpg';
        $pictureTests = array(
            //    Options, isValid Param, Expected value, Expected message
            array(
                'ed74c22109fe9f110579f77b053b8bc3',
                $testFile, true, ''
            ),
            array(
                '4d74c22109fe9f110579f77b053b8bc3',
                $testFile, false, 'fileMd5DoesNotMatch'
            ),
            array(
                array('4d74c22109fe9f110579f77b053b8bc3', 'ed74c22109fe9f110579f77b053b8bc3'),
                $testFile, true, ''
            ),
            array(
                array('4d74c22109fe9f110579f77b053b8bc3', '7d74c22109fe9f110579f77b053b8bc3'),
                $testFile, false, 'fileMd5DoesNotMatch'
            ),
        );

        $testFile = __DIR__ . '/_files/nofile.mo';
        $noFileTests = array(
            //    Options, isValid Param, Expected value, message
            array('ed74c22109fe9f110579f77b053b8bc3', $testFile, false, 'fileMd5NotFound'),
        );

        $testFile = __DIR__ . '/_files/testsize.mo';
        $sizeFileTests = array(
            //    Options, isValid Param, Expected value, message
            array('ec441f84a2944405baa22873cda22370', $testFile, true,  ''),
            array('7d74c22109fe9f110579f77b053b8bc3', $testFile, false, 'fileMd5DoesNotMatch'),
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
        $validator = new File\Md5($options);
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
            $validator = new File\Md5($options);
            $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
            if (!$expected) {
                $this->assertTrue(array_key_exists($messageKey, $validator->getMessages()));
            }
        }
    }

    /**
     * Ensures that getMd5() returns expected value
     *
     * @return void
     */
    public function testgetMd5()
    {
        $validator = new File\Md5('12345');
        $this->assertEquals(array('12345' => 'md5'), $validator->getMd5());

        $validator = new File\Md5(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'md5', '12333' => 'md5', '12344' => 'md5'), $validator->getMd5());
    }

    /**
     * Ensures that getHash() returns expected value
     *
     * @return void
     */
    public function testgetHash()
    {
        $validator = new File\Md5('12345');
        $this->assertEquals(array('12345' => 'md5'), $validator->getHash());

        $validator = new File\Md5(array('12345', '12333', '12344'));
        $this->assertEquals(array('12345' => 'md5', '12333' => 'md5', '12344' => 'md5'), $validator->getHash());
    }

    /**
     * Ensures that setMd5() returns expected value
     *
     * @return void
     */
    public function testSetMd5()
    {
        $validator = new File\Md5('12345');
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
        $validator = new File\Md5('12345');
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
        $validator = new File\Md5('12345');
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
        $validator = new File\Md5('12345');
        $validator->addHash('12344');
        $this->assertEquals(array('12345' => 'md5', '12344' => 'md5'), $validator->getMd5());

        $validator->addHash(array('12321', '12121'));
        $this->assertEquals(array('12345' => 'md5', '12344' => 'md5', '12321' => 'md5', '12121' => 'md5'), $validator->getMd5());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Md5('12345');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileMd5NotFound', $validator->getMessages()));
        $this->assertContains("does not exist", current($validator->getMessages()));
    }
}
