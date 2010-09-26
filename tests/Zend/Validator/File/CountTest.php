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

// Call Zend_Validate_File_CountTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_File_CountTest::main");
}

/**
 * Test helper
 */

/**
 * @see Zend_Validate_File_Count
 */

/**
 * @category   Zend
 * @package    Zend_Validate_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class CountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new \PHPUnit_Framework_TestSuite("Zend_Validate_File_CountTest");
        $result = \PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(5, true, true, true, true),
            array(array('min' => 0, 'max' => 3), true, true, true, false),
            array(array('min' => 2, 'max' => 3), false, true, true, false),
            array(array('min' => 2), false, true, true, true),
            array(array('max' => 5), true, true, true, true),
            );

        foreach ($valuesExpected as $element) {
            $validator = new File\Count($element[0]);
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/testsize.mo'),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[2],
                $validator->isValid(__DIR__ . '/_files/testsize2.mo'),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[3],
                $validator->isValid(__DIR__ . '/_files/testsize3.mo'),
                "Tested with " . var_export($element, 1)
            );
            $this->assertEquals(
                $element[4],
                $validator->isValid(__DIR__ . '/_files/testsize4.mo'),
                "Tested with " . var_export($element, 1)
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
        $validator = new File\Count(array('min' => 1, 'max' => 5));
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new File\Count(array('min' => 5, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Validator\Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }

        $validator = new File\Count(array('min' => 1, 'max' => 5));
        $this->assertEquals(1, $validator->getMin());

        try {
            $validator = new File\Count(array('min' => 5, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Validator\Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setMin() returns expected value
     *
     * @return void
     */
    public function testSetMin()
    {
        $validator = new File\Count(array('min' => 1000, 'max' => 10000));
        $validator->setMin(100);
        $this->assertEquals(100, $validator->getMin());

        try {
            $validator->setMin(20000);
            $this->fail("Missing exception");
        } catch (Validator\Exception $e) {
            $this->assertContains("less than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new File\Count(array('min' => 1, 'max' => 100));
        $this->assertEquals(100, $validator->getMax());

        try {
            $validator = new File\Count(array('min' => 5, 'max' => 1));
            $this->fail("Missing exception");
        } catch (Validator\Exception $e) {
            $this->assertContains("greater than or equal", $e->getMessage());
        }
    }

    /**
     * Ensures that setMax() returns expected value
     *
     * @return void
     */
    public function testSetMax()
    {
        $validator = new File\Count(array('min' => 1000, 'max' => 10000));
        $validator->setMax(1000000);
        $this->assertEquals(1000000, $validator->getMax());

        $validator->setMin(100);
        $this->assertEquals(1000000, $validator->getMax());
    }
}

// Call Zend_Validate_File_CountTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_File_CountTest::main") {
    \Zend_Validate_File_CountTest::main();
}
