<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use Zend\Filter\UriNormalize;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class UriNormalizeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider abnormalUriProvider
     */
    public function testUrisAreNormalized($url, $expected)
    {
        $filter = new UriNormalize();
        $result = $filter->filter($url);
        $this->assertEquals($expected, $result);
    }

    public function testDefaultSchemeAffectsNormalization()
    {
        $this->markTestIncomplete();
    }

    /**
     * @dataProvider enforcedSchemeTestcaseProvider
     */
    public function testEnforcedScheme($scheme, $input, $expected)
    {
        $filter = new UriNormalize(array('enforcedScheme' => $scheme));
        $result = $filter->filter($input);
        $this->assertEquals($expected, $result);
    }

    public static function abnormalUriProvider()
    {
        return array(
            array('http://www.example.com', 'http://www.example.com/'),
            array('hTTp://www.example.com/ space', 'http://www.example.com/%20space'),
            array('file:///www.example.com/foo/bar', 'file:///www.example.com/foo/bar'), // this should not be affected
            array('file:///home/shahar/secret/../../otherguy/secret', 'file:///home/otherguy/secret'),
            array('https://www.example.com:443/hasport', 'https://www.example.com/hasport'),
            array('/foo/bar?q=%711', '/foo/bar?q=q1'), // no scheme enforced
        );
    }

    public static function enforcedSchemeTestcaseProvider()
    {
        return array(
            array('ftp', 'http://www.example.com', 'http://www.example.com/'), // no effect - this one has a scheme
            array('mailto', 'mailto:shahar@example.com', 'mailto:shahar@example.com'),
            array('http', 'www.example.com/foo/bar?q=q', 'http://www.example.com/foo/bar?q=q'),
            array('ftp', 'www.example.com/path/to/file.ext', 'ftp://www.example.com/path/to/file.ext'),
            array('http', '/just/a/path', '/just/a/path') // cannot be enforced, no host
        );
    }
}
