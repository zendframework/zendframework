<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Validate/Sitemap/Priority.php';

/**
 * Tests Zend_Validate_Sitemap_Priority
 *
 */
class Zend_Validate_Sitemap_PriorityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validate_Sitemap_Priority
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new Zend_Validate_Sitemap_Priority();
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
            'alwayz',  '_hourly', 'Daily', 'wEekly',
            'mönthly ', ' yearly ', 'never ', 'rofl',
            '0,0', '1.1', '02', '3', '01.4', '0.f',
            1.1, -0.001, 1.0001
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }
}