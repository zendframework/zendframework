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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Tests Zym_Validate_Sitemap_Changefreq
 *
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_Sitemap_ChangefreqTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validate_Sitemap_Changefreq
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new Zend_Validate_Sitemap_Changefreq();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown()
    {
        $this->_validator = null;
    }

    /**
     * Tests valid change frequencies
     *
     */
    public function testValidChangefreqs()
    {
        $values = array(
            'always',  'hourly', 'daily', 'weekly',
            'monthly', 'yearly', 'never'
        );

        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }

    /**
     * Tests strings that should be invalid
     *
     */
    public function testInvalidStrings()
    {
        $values = array(
            'alwayz',  '_hourly', 'Daily', 'wEekly',
            'mönthly ', ' yearly ', 'never ', 'rofl',
            'yesterday',
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }

    /**
     * Tests values that are not strings
     *
     */
    public function testNotString()
    {
        $values = array(
            1, 1.4, null, new stdClass(), true, false
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }
}
