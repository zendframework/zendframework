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
use Zend\Validator;


/**
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class ImageSizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(array('minWidth' => 0,   'minHeight' => 10,  'maxWidth' => 1000, 'maxHeight' => 2000), true),
            array(array('minWidth' => 0,   'minHeight' => 0,   'maxWidth' => 200,  'maxHeight' => 200), true),
            array(array('minWidth' => 150, 'minHeight' => 150, 'maxWidth' => 200,  'maxHeight' => 200), false),
            array(array('minWidth' => 80,  'minHeight' => 0,   'maxWidth' => 80,   'maxHeight' => 200), true),
            array(array('minWidth' => 0,   'minHeight' => 0,   'maxWidth' => 60,   'maxHeight' => 200), false),
            array(array('minWidth' => 90,  'minHeight' => 0,   'maxWidth' => 200,  'maxHeight' => 200), false),
            array(array('minWidth' => 0,   'minHeight' => 0,   'maxWidth' => 200,  'maxHeight' => 80), false),
            array(array('minWidth' => 0,   'minHeight' => 110, 'maxWidth' => 200,  'maxHeight' => 140), false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new File\ImageSize($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new File\ImageSize(array('minWidth' => 0, 'minHeight' => 10, 'maxWidth' => 1000, 'maxHeight' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg'));
        $failures = $validator->getMessages();
        $this->assertContains('is not readable', $failures['fileImageSizeNotReadable']);

        $file['name'] = 'TestName';
        $validator = new File\ImageSize(array('minWidth' => 0, 'minHeight' => 10, 'maxWidth' => 1000, 'maxHeight' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg', $file));
        $failures = $validator->getMessages();
        $this->assertContains('TestName', $failures['fileImageSizeNotReadable']);

        $validator = new File\ImageSize(array('minWidth' => 0, 'minHeight' => 10, 'maxWidth' => 1000, 'maxHeight' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/badpicture.jpg'));
        $failures = $validator->getMessages();
        $this->assertContains('could not be detected', $failures['fileImageSizeNotDetected']);
    }

    /**
     * Ensures that getImageMin() returns expected value
     *
     * @return void
     */
    public function testGetImageMin()
    {
        $validator = new File\ImageSize(array('minWidth' => 1, 'minHeight' => 10, 'maxWidth' => 100, 'maxHeight' => 1000));
        $this->assertEquals(array('minWidth' => 1, 'minHeight' => 10), $validator->getImageMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\ImageSize(array('minWidth' => 1000, 'minHeight' => 100, 'maxWidth' => 10, 'maxHeight' => 1));
    }

    /**
     * Ensures that setImageMin() returns expected value
     *
     * @return void
     */
    public function testSetImageMin()
    {
        $validator = new File\ImageSize(array('minWidth' => 100, 'minHeight' => 1000, 'maxWidth' => 10000, 'maxHeight' => 100000));
        $validator->setImageMin(array('minWidth' => 10, 'minHeight' => 10));
        $this->assertEquals(array('minWidth' => 10, 'minHeight' => 10), $validator->getImageMin());

        $validator->setImageMin(array('minWidth' => 9, 'minHeight' => 100));
        $this->assertEquals(array('minWidth' => 9, 'minHeight' => 100), $validator->getImageMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageMin(array('minWidth' => 20000, 'minHeight' => 20000));
    }

    /**
     * Ensures that getImageMax() returns expected value
     *
     * @return void
     */
    public function testGetImageMax()
    {
        $validator = new File\ImageSize(array('minWidth' => 10, 'minHeight' => 100, 'maxWidth' => 1000, 'maxHeight' => 10000));
        $this->assertEquals(array('maxWidth' => 1000, 'maxHeight' => 10000), $validator->getImageMax());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\ImageSize(array('minWidth' => 10000, 'minHeight' => 1000, 'maxWidth' => 100, 'maxHeight' => 10));
    }

    /**
     * Ensures that setImageMax() returns expected value
     *
     * @return void
     */
    public function testSetImageMax()
    {
        $validator = new File\ImageSize(array('minWidth' => 10, 'minHeight' => 100, 'maxWidth' => 1000, 'maxHeight' => 10000));
        $validator->setImageMax(array('maxWidth' => 100, 'maxHeight' => 100));
        $this->assertEquals(array('maxWidth' => 100, 'maxHeight' => 100), $validator->getImageMax());

        $validator->setImageMax(array('maxWidth' => 110, 'maxHeight' => 1000));
        $this->assertEquals(array('maxWidth' => 110, 'maxHeight' => 1000), $validator->getImageMax());

        $validator->setImageMax(array('maxHeight' => 1100));
        $this->assertEquals(array('maxWidth' => 110, 'maxHeight' => 1100), $validator->getImageMax());

        $validator->setImageMax(array('maxWidth' => 120));
        $this->assertEquals(array('maxWidth' => 120, 'maxHeight' => 1100), $validator->getImageMax());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator->setImageMax(array('maxWidth' => 10000, 'maxHeight' => 1));
    }

    /**
     * Ensures that getImageWidth() returns expected value
     *
     * @return void
     */
    public function testGetImageWidth()
    {
        $validator = new File\ImageSize(array('minWidth' => 1, 'minHeight' => 10, 'maxWidth' => 100, 'maxHeight' => 1000));
        $this->assertEquals(array('minWidth' => 1, 'maxWidth' => 100), $validator->getImageWidth());
    }

    /**
     * Ensures that setImageWidth() returns expected value
     *
     * @return void
     */
    public function testSetImageWidth()
    {
        $validator = new File\ImageSize(array('minWidth' => 100, 'minHeight' => 1000, 'maxWidth' => 10000, 'maxHeight' => 100000));
        $validator->setImageWidth(array('minWidth' => 2000, 'maxWidth' => 2200));
        $this->assertEquals(array('minWidth' => 2000, 'maxWidth' => 2200), $validator->getImageWidth());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageWidth(array('minWidth' => 20000, 'maxWidth' => 200));
    }

    /**
     * Ensures that getImageHeight() returns expected value
     *
     * @return void
     */
    public function testGetImageHeight()
    {
        $validator = new File\ImageSize(array('minWidth' => 1, 'minHeight' => 10, 'maxWidth' => 100, 'maxHeight' => 1000));
        $this->assertEquals(array('minHeight' => 10, 'maxHeight' => 1000), $validator->getImageHeight());
    }

    /**
     * Ensures that setImageHeight() returns expected value
     *
     * @return void
     */
    public function testSetImageHeight()
    {
        $validator = new File\ImageSize(array('minWidth' => 100, 'minHeight' => 1000, 'maxWidth' => 10000, 'maxHeight' => 100000));
        $validator->setImageHeight(array('minHeight' => 2000, 'maxHeight' => 2200));
        $this->assertEquals(array('minHeight' => 2000, 'maxHeight' => 2200), $validator->getImageHeight());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageHeight(array('minHeight' => 20000, 'maxHeight' => 200));
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\ImageSize(array('minWidth' => 100, 'minHeight' => 1000, 'maxWidth' => 10000, 'maxHeight' => 100000));
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileImageSizeNotReadable', $validator->getMessages()));
        $this->assertContains("'nofile.mo'", current($validator->getMessages()));
    }
}
