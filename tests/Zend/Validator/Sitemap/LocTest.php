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
 * Tests Zend_Validator_Sitemap_Loc
 *
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class LocTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Validator
     *
     * @var Zend_Validator_Sitemap_Loc
     */
    protected $_validator;

    /**
     * Prepares the environment before running a test
     */
    protected function setUp()
    {
        $this->_validator = new \Zend\Validator\Sitemap\Loc();
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

    public static function invalidLocs()
    {
        return array(
            array('www.example.com'),
            array('/news/'),
            array('#'),
            array('http:/example.com/'),
            array('https://www.exmaple.com/?foo="bar\'&bar=<bat>'),
        );
    }

    /**
     * Tests invalid locations
     * @todo A change in the URI API has led to most of these now validating
     * @dataProvider invalidLocs
     */
    public function testInvalidLocs($url)
    {
        $this->markTestSkipped('Test must be reworked');
        $this->assertFalse($this->_validator->isValid($url), $url);
        $messages = $this->_validator->getMessages();
        $this->assertContains('is not a valid', current($messages));
    }

    /**
     * Tests values that are not strings
     *
     */
    public function testNotStrings()
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
