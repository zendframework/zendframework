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
use Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class SizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile = __DIR__ . '/_files/testsize.mo';
        $testData = array(
            //    Options, isValid Param, Expected value
            array(794,     $testFile,     true),
            array(500,     $testFile,     false),
            array(array('min' => 0, 'max' => 10000),      $testFile,   true),
            array(array('min' => 0, 'max' => '10 MB'),    $testFile,   true),
            array(array('min' => '4B', 'max' => '10 MB'), $testFile,   true),
            array(array('min' => 0, 'max' => '10MB'),     $testFile,   true),
            array(array('min' => 0, 'max' => '10  MB'),   $testFile,   true),
            array(array('min' => 794),                    $testFile,   true),
            array(array('min' => 0, 'max' => 500),        $testFile,   false),
        );

        // Dupe data in File Upload format
        foreach ($testData as $data) {
            $fileUpload = array(
                'tmp_name' => $data[1], 'name' => basename($data[1]),
                'size' => 200, 'error' => 0, 'type' => 'text'
            );
            $testData[] = array($data[0], $fileUpload, $data[2]);
        }
        return $testData;
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testBasic($options, $isValidParam, $expected)
    {
        $validator = new File\Size($options);
        $this->assertEquals($expected, $validator->isValid($isValidParam));
    }

    /**
     * Ensures that the validator follows expected behavior for legacy Zend\Transfer API
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testLegacy($options, $isValidParam, $expected)
    {
        if (is_array($isValidParam)) {
            $validator = new File\Size($options);
            $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
        }
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new File\Size(array('min' => 1, 'max' => 100));
        $this->assertEquals('1B', $validator->getMin());

        $validator = new File\Size(array('min' => 1, 'max' => 100, 'useByteString' => false));
        $this->assertEquals(1, $validator->getMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\Size(array('min' => 100, 'max' => 1));
    }

    /**
     * Ensures that setMin() returns expected value
     *
     * @return void
     */
    public function testSetMin()
    {
        $validator = new File\Size(array('min' => 1000, 'max' => 10000));
        $validator->setMin(100);
        $this->assertEquals('100B', $validator->getMin());

        $validator = new File\Size(array('min' => 1000, 'max' => 10000, 'useByteString' => false));
        $validator->setMin(100);
        $this->assertEquals(100, $validator->getMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setMin(20000);
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new File\Size(array('min' => 1, 'max' => 100, 'useByteString' => false));
        $this->assertEquals(100, $validator->getMax());

        $validator = new File\Size(array('min' => 1, 'max' => 100000));
        $this->assertEquals('97.66kB', $validator->getMax());

        $validator = new File\Size(2000);
        $this->assertEquals('1.95kB', $validator->getMax());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\Size(array('min' => 100, 'max' => 1));
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax()
    {
        $validator = new File\Size(array('max' => 0, 'useByteString' => true));
        $this->assertEquals('0B', $validator->getMax());

        $validator->setMax(1000000);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals('976.56kB', $validator->getMax());

        $validator->setMax('100 AB');
        $this->assertEquals('100B', $validator->getMax());

        $validator->setMax('100 kB');
        $this->assertEquals('100kB', $validator->getMax());

        $validator->setMax('100 MB');
        $this->assertEquals('100MB', $validator->getMax());

        $validator->setMax('1 GB');
        $this->assertEquals('1GB', $validator->getMax());

        $validator->setMax('0.001 TB');
        $this->assertEquals('1.02GB', $validator->getMax());

        $validator->setMax('0.000001 PB');
        $this->assertEquals('1.05GB', $validator->getMax());

        $validator->setMax('0.000000001 EB');
        $this->assertEquals('1.07GB', $validator->getMax());

        $validator->setMax('0.000000000001 ZB');
        $this->assertEquals('1.1GB', $validator->getMax());

        $validator->setMax('0.000000000000001 YB');
        $this->assertEquals('1.13GB', $validator->getMax());
    }

    /**
     * Ensures that the validator returns size infos
     *
     * @return void
     */
    public function testFailureMessage()
    {
        $validator = new File\Size(array('min' => 9999, 'max' => 10000));
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/testsize.mo'));
        $messages = $validator->getMessages();
        $this->assertContains('9.76kB', current($messages));
        $this->assertContains('794B', current($messages));

        $validator = new File\Size(array('min' => 9999, 'max' => 10000, 'useByteString' => false));
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/testsize.mo'));
        $messages = $validator->getMessages();
        $this->assertContains('9999', current($messages));
        $this->assertContains('794', current($messages));
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Size(array('min' => 1, 'max' => 10000));
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileSizeNotFound', $validator->getMessages()));
        $this->assertContains("does not exist", current($validator->getMessages()));
    }
}
