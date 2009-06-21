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
 * @version    $Id$
 */

// Call Zend_Validate_File_ExcludeMimeTypeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_ExcludeMimeTypeTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Validate_File_ExcludeMimeType
 */
require_once 'Zend/Validate/File/ExcludeMimeType.php';

/**
 * ExcludeMimeType testbed
 *
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_ExcludeMimeTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_File_ExcludeMimeTypeTest");
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
            array('image/gif', false),
            array('image', false),
            array('test/notype', true),
            array('image/gif, image/jpeg', false),
            array(array('image/vasa', 'image/gif'), false),
            array(array('image/jpeg', 'gif'), false),
            array(array('image/jpeg', 'jpeg'), true),
        );

        $files = array(
            'name'     => 'testsize.mo',
            'type'     => 'image/gif',
            'size'     => 200,
            'tmp_name' => dirname(__FILE__) . '/_files/testsize.mo',
            'error'    => 0
        );

        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_File_ExcludeMimeType($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(dirname(__FILE__) . '/_files/testsize.mo', $files),
                "Tested with " . var_export($element, 1)
            );
        }
    }

    /**
     * Ensures that getMimeType() returns expected value
     *
     * @return void
     */
    public function testGetMimeType()
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $this->assertEquals('image/gif', $validator->getMimeType());

        $validator = new Zend_Validate_File_ExcludeMimeType(array('image/gif', 'video', 'text/test'));
        $this->assertEquals('image/gif,video,text/test', $validator->getMimeType());

        $validator = new Zend_Validate_File_ExcludeMimeType(array('image/gif', 'video', 'text/test'));
        $this->assertEquals(array('image/gif', 'video', 'text/test'), $validator->getMimeType(true));
    }

    /**
     * Ensures that setMimeType() returns expected value
     *
     * @return void
     */
    public function testSetMimeType()
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $validator->setMimeType('image/jpeg');
        $this->assertEquals('image/jpeg', $validator->getMimeType());
        $this->assertEquals(array('image/jpeg'), $validator->getMimeType(true));
        
        $validator->setMimeType('image/gif, text/test');
        $this->assertEquals('image/gif,text/test', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text/test'), $validator->getMimeType(true));
        
        $validator->setMimeType(array('video/mpeg', 'gif'));
        $this->assertEquals('video/mpeg,gif', $validator->getMimeType());
        $this->assertEquals(array('video/mpeg', 'gif'), $validator->getMimeType(true));
    }

    /**
     * Ensures that addMimeType() returns expected value
     *
     * @return void
     */
    public function testAddMimeType()
    {
        $validator = new Zend_Validate_File_ExcludeMimeType('image/gif');
        $validator->addMimeType('text');
        $this->assertEquals('image/gif,text', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text'), $validator->getMimeType(true));

        $validator->addMimeType('jpg, to');
        $this->assertEquals('image/gif,text,jpg,to', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to'), $validator->getMimeType(true));

        $validator->addMimeType(array('zip', 'ti'));
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to', 'zip', 'ti'), $validator->getMimeType(true));

        $validator->addMimeType('');
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to', 'zip', 'ti'), $validator->getMimeType(true));
    }
}

// Call Zend_Validate_File_ExcludeMimeTypeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_ExcludeMimeTypeTest::main") {
    Zend_Validate_File_ExcludeMimeTypeTest::main();
}
