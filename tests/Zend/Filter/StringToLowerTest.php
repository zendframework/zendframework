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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_StringToLower
 */
require_once 'Zend/Filter/StringToLower.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_StringToLowerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_StringToLower object
     *
     * @var Zend_Filter_StringToLower
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_StringToLower object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_StringToLower();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            'string' => 'string',
            'aBc1@3' => 'abc1@3',
            'A b C'  => 'a b c'
        );

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $this->_filter->filter($input));
        }
    }

    /**
     * Ensures that the filter follows expected behavior with
     * specified encoding
     *
     * @return void
     */
    public function testWithEncoding()
    {
        $valuesExpected = array(
            'Ü'     => 'ü',
            'Ñ'     => 'ñ',
            'ÜÑ123' => 'üñ123'
        );

        try {
            $this->_filter->setEncoding('UTF-8');
            foreach ($valuesExpected as $input => $output) {
                $this->assertEquals($output, $this->_filter->filter($input));
            }
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('mbstring is required', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testFalseEncoding()
    {
        if (!function_exists('mb_strtolower')) {
            $this->markTestSkipped('mbstring required');
        }

        try {
            $this->_filter->setEncoding('aaaaa');
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('is not supported', $e->getMessage());
        }
    }
}
