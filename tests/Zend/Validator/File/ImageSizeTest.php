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

/**
 * @namespace
 */
namespace ZendTest\Validate\File;
use Zend\Validator\File;
use Zend\Validator;


/**
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
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
            array(array('minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000), true),
            array(array('minwidth' => 0, 'minheight' => 0, 'maxwidth' => 200, 'maxheight' => 200), true),
            array(array('minwidth' => 150, 'minheight' => 150, 'maxwidth' => 200, 'maxheight' => 200), false),
            array(array('minwidth' => 80, 'minheight' => 0, 'maxwidth' => 80, 'maxheight' => 200), true),
            array(array('minwidth' => 0, 'minheight' => 0, 'maxwidth' => 60, 'maxheight' => 200), false),
            array(array('minwidth' => 90, 'minheight' => 0, 'maxwidth' => 200, 'maxheight' => 200), false),
            array(array('minwidth' => 0, 'minheight' => 0, 'maxwidth' => 200, 'maxheight' => 80), false),
            array(array('minwidth' => 0, 'minheight' => 110, 'maxwidth' => 200, 'maxheight' => 140), false)
        );

        foreach ($valuesExpected as $element) {
            $validator = new File\ImageSize($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/picture.jpg'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new File\ImageSize(array('minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg'));
        $failures = $validator->getMessages();
        $this->assertContains('is not readable', $failures['fileImageSizeNotReadable']);

        $file['name'] = 'TestName';
        $validator = new File\ImageSize(array('minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.jpg', $file));
        $failures = $validator->getMessages();
        $this->assertContains('TestName', $failures['fileImageSizeNotReadable']);

        $validator = new File\ImageSize(array('minwidth' => 0, 'minheight' => 10, 'maxwidth' => 1000, 'maxheight' => 2000));
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
        $validator = new File\ImageSize(array('minwidth' => 1, 'minheight' => 10, 'maxwidth' => 100, 'maxheight' => 1000));
        $this->assertEquals(array('minwidth' => 1, 'minheight' => 10), $validator->getImageMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\ImageSize(array('minwidth' => 1000, 'minheight' => 100, 'maxwidth' => 10, 'maxheight' => 1));
    }

    /**
     * Ensures that setImageMin() returns expected value
     *
     * @return void
     */
    public function testSetImageMin()
    {
        $validator = new File\ImageSize(array('minwidth' => 100, 'minheight' => 1000, 'maxwidth' => 10000, 'maxheight' => 100000));
        $validator->setImageMin(array('minwidth' => 10, 'minheight' => 10));
        $this->assertEquals(array('minwidth' => 10, 'minheight' => 10), $validator->getImageMin());

        $validator->setImageMin(array('minwidth' => 9, 'minheight' => 100));
        $this->assertEquals(array('minwidth' => 9, 'minheight' => 100), $validator->getImageMin());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageMin(array('minwidth' => 20000, 'minheight' => 20000));
    }

    /**
     * Ensures that getImageMax() returns expected value
     *
     * @return void
     */
    public function testGetImageMax()
    {
        $validator = new File\ImageSize(array('minwidth' => 10, 'minheight' => 100, 'maxwidth' => 1000, 'maxheight' => 10000));
        $this->assertEquals(array('maxwidth' => 1000, 'maxheight' => 10000), $validator->getImageMax());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator = new File\ImageSize(array('minwidth' => 10000, 'minheight' => 1000, 'maxwidth' => 100, 'maxheight' => 10));
    }

    /**
     * Ensures that setImageMax() returns expected value
     *
     * @return void
     */
    public function testSetImageMax()
    {
        $validator = new File\ImageSize(array('minwidth' => 10, 'minheight' => 100, 'maxwidth' => 1000, 'maxheight' => 10000));
        $validator->setImageMax(array('maxwidth' => 100, 'maxheight' => 100));
        $this->assertEquals(array('maxwidth' => 100, 'maxheight' => 100), $validator->getImageMax());

        $validator->setImageMax(array('maxwidth' => 110, 'maxheight' => 1000));
        $this->assertEquals(array('maxwidth' => 110, 'maxheight' => 1000), $validator->getImageMax());

        $validator->setImageMax(array('maxheight' => 1100));
        $this->assertEquals(array('maxwidth' => 110, 'maxheight' => 1100), $validator->getImageMax());

        $validator->setImageMax(array('maxwidth' => 120));
        $this->assertEquals(array('maxwidth' => 120, 'maxheight' => 1100), $validator->getImageMax());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'greater than or equal');
        $validator->setImageMax(array('maxwidth' => 10000, 'maxheight' => 1));
    }

    /**
     * Ensures that getImageWidth() returns expected value
     *
     * @return void
     */
    public function testGetImageWidth()
    {
        $validator = new File\ImageSize(array('minwidth' => 1, 'minheight' => 10, 'maxwidth' => 100, 'maxheight' => 1000));
        $this->assertEquals(array('minwidth' => 1, 'maxwidth' => 100), $validator->getImageWidth());
    }

    /**
     * Ensures that setImageWidth() returns expected value
     *
     * @return void
     */
    public function testSetImageWidth()
    {
        $validator = new File\ImageSize(array('minwidth' => 100, 'minheight' => 1000, 'maxwidth' => 10000, 'maxheight' => 100000));
        $validator->setImageWidth(array('minwidth' => 2000, 'maxwidth' => 2200));
        $this->assertEquals(array('minwidth' => 2000, 'maxwidth' => 2200), $validator->getImageWidth());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageWidth(array('minwidth' => 20000, 'maxwidth' => 200));
    }

    /**
     * Ensures that getImageHeight() returns expected value
     *
     * @return void
     */
    public function testGetImageHeight()
    {
        $validator = new File\ImageSize(array('minwidth' => 1, 'minheight' => 10, 'maxwidth' => 100, 'maxheight' => 1000));
        $this->assertEquals(array('minheight' => 10, 'maxheight' => 1000), $validator->getImageHeight());
    }

    /**
     * Ensures that setImageHeight() returns expected value
     *
     * @return void
     */
    public function testSetImageHeight()
    {
        $validator = new File\ImageSize(array('minwidth' => 100, 'minheight' => 1000, 'maxwidth' => 10000, 'maxheight' => 100000));
        $validator->setImageHeight(array('minheight' => 2000, 'maxheight' => 2200));
        $this->assertEquals(array('minheight' => 2000, 'maxheight' => 2200), $validator->getImageHeight());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'less than or equal');
        $validator->setImageHeight(array('minheight' => 20000, 'maxheight' => 200));
    }
}
