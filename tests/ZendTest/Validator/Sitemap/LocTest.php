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

use Zend\Validator\Sitemap\Loc;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class LocTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loc
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new Loc();
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
            $this->assertSame(true, $this->validator->isValid($value));
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
        $this->markTestIncomplete('Test must be reworked');
        $this->assertFalse($this->validator->isValid($url), $url);
        $messages = $this->validator->getMessages();
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
            $this->assertSame(false, $this->validator->isValid($value));
            $messages = $this->validator->getMessages();
            $this->assertContains('String expected', current($messages));
        }
    }

}
