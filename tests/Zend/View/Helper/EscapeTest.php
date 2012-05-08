<?php

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\View\Helper\Escape as EscapeHelper;

class EscapeTest extends TestCase
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

    public function testEncodingIsMutable()
    {
        $this->helper->setEncoding('BIG5-HKSCS');
        $this->assertEquals('BIG5-HKSCS', $this->helper->getEncoding());
    }

    public function testDefaultCallbackIsDefined()
    {
        $callback = $this->helper->getCallback();
        $this->assertTrue(is_callable($callback));
    }

    public function testCallbackIsMutable()
    {
        $this->helper->setCallback('strip_tags'); // Don't do this at home ;)
        $this->assertEquals('strip_tags', $this->helper->getCallback());
    }

    public function testSettingInvalidCallbackRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\ExceptionInterface');
        $this->helper->setCallback(3.1415);
    }

    public function testInvokingCallbackEscapesText()
    {
        $text     = 'Hey! <b>This is some Text!</b> Yo!';
        $expected = htmlspecialchars($text, ENT_COMPAT, 'UTF-8', false);
        $test     = $this->helper->__invoke($text);
        $this->assertEquals($expected, $test);
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
            'foo' => '&lt;b&gt;bar&lt;/b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;/em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;/i&gt;',
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
        $this->assertEquals(get_class($object), $test);
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
            'foo' => '&lt;b&gt;bar&lt;/b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;/em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;/i&gt;',
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
            'foo' => '&lt;b&gt;bar&lt;/b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;/em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;/i&gt;',
                ),
            ),
        );
        $test = $this->helper->__invoke($object, EscapeHelper::RECURSE_OBJECT);
        $this->assertEquals($expected, $test);
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     * 
     * PHP 5.3 instates default encoding on empty string instead of the expected
     * warning level error for htmlspecialchars() encoding param. PHP 5.4 attempts
     * to guess the encoding or take it from php.ini default_charset when an empty
     * string is set. Both are insecure behaviours.
     */
    public function testSettingEncodingToEmptyStringShouldThrowException()
    {
        $this->helper->setEncoding('');
    }

    public function testSettingValidEncodingShouldNotThrowExceptions()
    {
        foreach ($this->supportedEncodings as $value) {
            $this->helper->setEncoding($value);
        }
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     * 
     * All versions of PHP - when an invalid encoding is set on htmlspecialchars()
     * a warning level error is issued and escaping continues with the default encoding
     * for that PHP version. Preventing the continuation behaviour offsets display_errors
     * off in production env.
     */
    public function testSettingEncodingToInvalidValueShouldThrowException()
    {
        $this->helper->setEncoding('completely-invalid');
    }
}
