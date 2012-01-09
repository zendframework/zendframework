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
 * @package    Zend_Translator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Validator\Sitemap;

/**
 * Tests Zend_Validator_Sitemap_Priority
 *
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validator_Sitemap_Priority
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new \Zend\Validator\Sitemap\Priority();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown()
    {
        $this->_validator = null;
    }

    /**
     * Tests valid priorities
     *
     */
    public function testValidPriorities()
    {
        $values = array(
            '0.0', '0.1', '0.2', '0.3', '0.4', '0.5',
            '0.6', '0.7', '0.8', '0.9', '1.0', '0.99',
            0.1, 0.6667, 0.0001, 0.4, 0, 1, .35
        );

        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }

    /**
     * Tests invalid priorities
     *
     */
    public function testInvalidPriorities()
    {
        $values = array(
            -1, -0.1, 1.1, 100, 10, 2, '3', '-4',
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertContains('is not a valid', current($messages));
        }
    }

    /**
     * Tests values that are no numbers
     *
     */
    public function testNotNumbers()
    {
        $values = array(
            null, new \stdClass(), true, false, 'abcd',
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
            $messages = $this->_validator->getMessages();
            $this->assertContains('integer or float expected', current($messages));
        }
    }
}
