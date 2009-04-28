<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Validate/Sitemap/Changefreq.php';

/**
 * Tests Zym_Validate_Sitemap_Changefreq
 *
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