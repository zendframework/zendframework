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
 * @see Zend_Validator_File_Extension
 */

/**
 * Extension testbed
 *
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('mo', true),
            array('gif', false),
            array(array('mo'), true),
            array(array('gif'), false),
            array(array('gif', 'pdf', 'mo', 'pict'), true),
            array(array('gif', 'gz', 'hint'), false),
        );

        foreach ($valuesExpected as $element) {
            $validator = new File\Extension($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
        }

        $validator = new File\Extension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileExtensionNotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'test1',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => 'tmp_test1',
            'error'    => 0
        );
        $validator = new File\Extension('mo');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/nofile.mo', $files));
        $this->assertTrue(array_key_exists('fileExtensionNotFound', $validator->getMessages()));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new File\Extension('mo');
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new File\Extension('gif');
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
        $this->assertTrue(array_key_exists('fileExtensionFalse', $validator->getMessages()));
    }

    public function testZF3891()
    {
        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'text',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/testsize.mo',
            'error'    => 0
        );
        $validator = new File\Extension(array('MO', 'case' => true));
        $this->assertEquals(false, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));

        $validator = new File\Extension(array('MO', 'case' => false));
        $this->assertEquals(true, $validator->isValid(__DIR__ . '/_files/testsize.mo', $files));
    }

    /**
     * Ensures that getExtension() returns expected value
     *
     * @return void
     */
    public function testGetExtension()
    {
        $validator = new File\Extension('mo');
        $this->assertEquals(array('mo'), $validator->getExtension());

        $validator = new File\Extension(array('mo', 'gif', 'jpg'));
        $this->assertEquals(array('mo', 'gif', 'jpg'), $validator->getExtension());
    }

    /**
     * Ensures that setExtension() returns expected value
     *
     * @return void
     */
    public function testSetExtension()
    {
        $validator = new File\Extension('mo');
        $validator->setExtension('gif');
        $this->assertEquals(array('gif'), $validator->getExtension());

        $validator->setExtension('jpg, mo');
        $this->assertEquals(array('jpg', 'mo'), $validator->getExtension());

        $validator->setExtension(array('zip', 'ti'));
        $this->assertEquals(array('zip', 'ti'), $validator->getExtension());
    }

    /**
     * Ensures that addExtension() returns expected value
     *
     * @return void
     */
    public function testAddExtension()
    {
        $validator = new File\Extension('mo');
        $validator->addExtension('gif');
        $this->assertEquals(array('mo', 'gif'), $validator->getExtension());

        $validator->addExtension('jpg, to');
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to'), $validator->getExtension());

        $validator->addExtension(array('zip', 'ti'));
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to', 'zip', 'ti'), $validator->getExtension());

        $validator->addExtension('');
        $this->assertEquals(array('mo', 'gif', 'jpg', 'to', 'zip', 'ti'), $validator->getExtension());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Extension('gif');
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileExtensionNotFound', $validator->getMessages()));
        $this->assertContains("'nofile.mo'", current($validator->getMessages()));
    }
}
