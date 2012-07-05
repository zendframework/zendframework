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

namespace ZendTest\Validator\File;

use Zend\Validator\File;
use Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class SizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(array('min' => 0, 'max' => 10000), true),
            array(array('min' => 0, 'max' => '10 MB'), true),
            array(array('min' => '4B', 'max' => '10 MB'), true),
            array(array('min' => 0, 'max' => '10MB'), true),
            array(array('min' => 0, 'max' => '10  MB'), true),
            array(794, true),
            array(array('min' => 794), true),
            array(array('min' => 0, 'max' => 500), false),
            array(500, false),
        );

        foreach ($valuesExpected as $element) {
            $options = array_shift($element);
            $value   = array_shift($element);
            $validator = new File\Size($options);
            $this->assertEquals(
                $value,
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                "Tested " . var_export($value, 1) . " against options " . var_export($options, 1)
            );
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
        $this->assertContains("'nofile.mo'", current($validator->getMessages()));
    }
}
