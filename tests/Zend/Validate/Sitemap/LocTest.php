<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Validate/Sitemap/Loc.php';

/**
 * Tests Zend_Validate_Sitemap_Loc
 *
 */
class Zend_Validate_Sitemap_LocTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validate_Sitemap_Loc
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new Zend_Validate_Sitemap_Loc();
    }

    /**
     * Cleans up the environment after running a test
     */
    protected function tearDown()
    {
        $this->_validator = null;
    }

    /**
     * Tests valid locations
     *
     */
    public function testValidLocs()
    {
        $values = array(
            'http://www.example.com',
            'http://www.example.com/',
            'http://www.exmaple.lan/',
            'https://www.exmaple.com/?foo=bar',
            'http://www.exmaple.com:8080/foo/bar/',
            'https://user:pass@www.exmaple.com:8080/',
            'https://www.exmaple.com/?foo=&quot;bar&apos;&amp;bar=&lt;bat&gt;'
        );

        foreach ($values as $value) {
            $this->assertSame(true, $this->_validator->isValid($value));
        }
    }

    /**
     * Tests invalid locations
     *
     */
    public function testInvalidLocs()
    {
        $values = array(
            'www.example.com',
            '/news/',
            '#',
            new stdClass(),
            42,
            'http:/example.com/',
            null,
            'https://www.exmaple.com/?foo="bar\'&bar=<bat>'
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->_validator->isValid($value));
        }
    }
}