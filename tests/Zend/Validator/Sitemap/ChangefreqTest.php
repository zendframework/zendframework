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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Validator\Sitemap;

/**
 * Tests Zym_Validator_Sitemap_Changefreq
 *
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class ChangefreqTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validator_Sitemap_Changefreq
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new \Zend\Validator\Sitemap\Changefreq();
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
            $messages = $this->_validator->getMessages();
            $this->assertContains('is not a valid', current($messages));
        }
    }

    /**
     * Tests values that are not strings
     *
     */
    public function testNotString()
    {
        $values = array(
            1, 1.4, null, new \stdClass(), true, false
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertContains('String expected', current($messages));
        }
    }
}
