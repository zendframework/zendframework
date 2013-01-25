<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\Sitemap;

use Zend\Validator\Sitemap\Priority;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Priority
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new Priority();
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
            $this->assertSame(true, $this->validator->isValid($value));
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
            $this->assertSame(false, $this->validator->isValid($value));
            $messages = $this->validator->getMessages();
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
            $this->assertSame(false, $this->validator->isValid($value));
            $messages = $this->validator->getMessages();
            $this->assertContains('integer or float expected', current($messages));
        }
    }
}
