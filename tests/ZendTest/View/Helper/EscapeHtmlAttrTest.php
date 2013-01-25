<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\View\Helper\EscapeHtmlAttr as EscapeHelper;

class EscapeHtmlAttrTest extends TestCase
{

    protected $supportedEncodings = array(
        'iso-8859-1',   'iso8859-1',    'iso-8859-5',   'iso8859-5',
        'iso-8859-15',  'iso8859-15',   'utf-8',        'cp866',
        'ibm866',       '866',          'cp1251',       'windows-1251',
        'win-1251',     '1251',         'cp1252',       'windows-1252',
        '1252',         'koi8-r',       'koi8-ru',      'koi8r',
        'big5',         '950',          'gb2312',       '936',
        'big5-hkscs',   'shift_jis',    'sjis',         'sjis-win',
        'cp932',        '932',          'euc-jp',       'eucjp',
        'eucjp-win',    'macroman'
    );

    public function setUp()
    {
        $this->helper = new EscapeHelper;
    }

    public function testUsesUtf8EncodingByDefault()
    {
        $this->assertEquals('UTF-8', $this->helper->getEncoding());
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testEncodingIsImmutable()
    {
        $this->helper->setEncoding('BIG5-HKSCS');
        $this->helper->getEscaper();
        $this->helper->setEncoding('UTF-8');
    }

    public function testGetEscaperCreatesDefaultInstanceWithCorrectEncoding()
    {
        $this->helper->setEncoding('BIG5-HKSCS');
        $escaper = $this->helper->getEscaper();
        $this->assertTrue($escaper instanceof \Zend\Escaper\Escaper);
        $this->assertEquals('big5-hkscs', $escaper->getEncoding());
    }

    public function testSettingEscaperObjectAlsoSetsEncoding()
    {
        $escaper = new \Zend\Escaper\Escaper('big5-hkscs');
        $this->helper->setEscaper($escaper);
        $escaper = $this->helper->getEscaper();
        $this->assertTrue($escaper instanceof \Zend\Escaper\Escaper);
        $this->assertEquals('big5-hkscs', $escaper->getEncoding());
    }

    public function testEscapehtmlCalledOnEscaperObject()
    {
        $escaper = $this->getMock('\\Zend\\Escaper\\Escaper');
        $escaper->expects($this->any())->method('escapeHtmlAttr');
        $this->helper->setEscaper($escaper);
        $this->helper->__invoke('foo');
    }

    public function testAllowsRecursiveEscapingOfArrays()
    {
        $original = array(
            'foo' => '<b>bar</b>',
            'baz' => array(
                '<em>bat</em>',
                'second' => array(
                    '<i>third</i>',
                ),
            ),
        );
        $expected = array(
            'foo' => '&lt;b&gt;bar&lt;&#x2F;b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;&#x2F;em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;&#x2F;i&gt;',
                ),
            ),
        );
        $test = $this->helper->__invoke($original, EscapeHelper::RECURSE_ARRAY);
        $this->assertEquals($expected, $test);
    }

    public function testWillCastObjectsToStringsBeforeEscaping()
    {
        $object = new TestAsset\Stringified;
        $test = $this->helper->__invoke($object);
        $this->assertEquals(
            'ZendTest&#x5C;View&#x5C;Helper&#x5C;TestAsset&#x5C;Stringified',
            $test
        );
    }

    public function testCanRecurseObjectImplementingToArray()
    {
        $original = array(
            'foo' => '<b>bar</b>',
            'baz' => array(
                '<em>bat</em>',
                'second' => array(
                    '<i>third</i>',
                ),
            ),
        );
        $object = new TestAsset\ToArray();
        $object->array = $original;

        $expected = array(
            'foo' => '&lt;b&gt;bar&lt;&#x2F;b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;&#x2F;em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;&#x2F;i&gt;',
                ),
            ),
        );
        $test = $this->helper->__invoke($object, EscapeHelper::RECURSE_OBJECT);
        $this->assertEquals($expected, $test);
    }

    public function testCanRecurseObjectProperties()
    {
        $original = array(
            'foo' => '<b>bar</b>',
            'baz' => array(
                '<em>bat</em>',
                'second' => array(
                    '<i>third</i>',
                ),
            ),
        );
        $object = new stdClass();
        foreach ($original as $key => $value) {
            $object->$key = $value;
        }

        $expected = array(
            'foo' => '&lt;b&gt;bar&lt;&#x2F;b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;&#x2F;em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;&#x2F;i&gt;',
                ),
            ),
        );
        $test = $this->helper->__invoke($object, EscapeHelper::RECURSE_OBJECT);
        $this->assertEquals($expected, $test);
    }

    /**
     * @expectedException \Zend\Escaper\Exception\InvalidArgumentException
     *
     * PHP 5.3 instates default encoding on empty string instead of the expected
     * warning level error for htmlspecialchars() encoding param. PHP 5.4 attempts
     * to guess the encoding or take it from php.ini default_charset when an empty
     * string is set. Both are insecure behaviours.
     */
    public function testSettingEncodingToEmptyStringShouldThrowException()
    {
        $this->helper->setEncoding('');
        $this->helper->getEscaper();
    }

    public function testSettingValidEncodingShouldNotThrowExceptions()
    {
        foreach ($this->supportedEncodings as $value) {
            $helper = new EscapeHelper;
            $helper->setEncoding($value);
            $helper->getEscaper();
        }
    }

    /**
     * @expectedException \Zend\Escaper\Exception\InvalidArgumentException
     *
     * All versions of PHP - when an invalid encoding is set on htmlspecialchars()
     * a warning level error is issued and escaping continues with the default encoding
     * for that PHP version. Preventing the continuation behaviour offsets display_errors
     * off in production env.
     */
    public function testSettingEncodingToInvalidValueShouldThrowException()
    {
        $this->helper->setEncoding('completely-invalid');
        $this->helper->getEscaper();
    }
}
