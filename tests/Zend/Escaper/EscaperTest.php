<?php

namespace ZendTest\Escaper;

use Zend\Escaper\Escaper;

class EscaperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * All character encodings supported by htmlspecialchars()
     */
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

    protected $htmlSpecialChars = array(
        '\''    => '&#039;',
        '"'     => '&quot;',
        '<'     => '&lt;',
        '>'     => '&gt;',
        '&'     => '&amp;'
    );

    protected $jsSpecialChars = array(
        /* HTML special chars - escape without exception to hex */
        '<'     => '\\x3C',
        '>'     => '\\x3E',
        '\''    => '\\x27',
        '"'     => '\\x22',
        '&'     => '\\x26',
        /* Characters beyond ASCII value 255 to unicode escape */
        'Ā'     => '\\u0100',
        /* Immune chars excluded */
        ','     => ',',
        '.'     => '.',
        '_'     => '_',
        ' '     => ' ',
        /* Basic alnums exluded */
        'a'     => 'a',
        'A'     => 'A',
        'z'     => 'z',
        'Z'     => 'Z',
        '0'     => '0',
        '9'     => '9',
        /* Basic control characters and null */
        "\r"    => '\\x0D',
        "\n"    => '\\x0A',
        "\t"    => '\\x09',
        "\0"    => '\\x00',
        /* Encode spaces for quoteless attribute protection */
        ' '     => '\\x20',
    );

    protected $urlSpecialChars = array(
        /* HTML special chars - escape without exception to percent encoding */
        '<'     => '%3C',
        '>'     => '%3E',
        '\''    => '%27',
        '"'     => '%22',
        '&'     => '%26',
        /* Characters beyond ASCII value 255 to hex sequence */
        'Ā'     => '%C4%80',
        /* Punctuation and unreserved check */
        ','     => '%2C',
        '.'     => '.',
        '_'     => '_',
        '-'     => '-',
        ':'     => '%3A',
        ';'     => '%3B',
        '!'     => '%21',
        /* Basic alnums exluded */
        'a'     => 'a',
        'A'     => 'A',
        'z'     => 'z',
        'Z'     => 'Z',
        '0'     => '0',
        '9'     => '9',
        /* Basic control characters and null */
        "\r"    => '%0D',
        "\n"    => '%0A',
        "\t"    => '%09',
        "\0"    => '%00',
        /* PHP quirks from the past */
        ' '     => '%20',
        '~'     => '~',
        '+'     => '%2B',
    );

    protected $cssSpecialChars = array(
        /* HTML special chars - escape without exception to hex */
        '<'     => '\\3C ',
        '>'     => '\\3E ',
        '\''    => '\\27 ',
        '"'     => '\\22 ',
        '&'     => '\\26 ',
        /* Characters beyond ASCII value 255 to unicode escape */
        'Ā'     => '\\100 ',
        /* Immune chars excluded */
        ','     => '\\2C ',
        '.'     => '\\2E ',
        '_'     => '\\5F ',
        /* Basic alnums exluded */
        'a'     => 'a',
        'A'     => 'A',
        'z'     => 'z',
        'Z'     => 'Z',
        '0'     => '0',
        '9'     => '9',
        /* Basic control characters and null */
        "\r"    => '\\D ',
        "\n"    => '\\A ',
        "\t"    => '\\9 ',
        "\0"    => '\\0 ',
        /* Encode spaces for quoteless attribute protection */
        ' '     => '\\20 ',
    );
        

    public function setUp()
    {
        $this->escaper = new Escaper('UTF-8');
    }

    /**
     * @expectedException \Zend\Escaper\Exception\InvalidArgumentException
     */
    public function testSettingEncodingToEmptyStringShouldThrowException()
    {
        $escaper = new Escaper('');
    }

    public function testSettingValidEncodingShouldNotThrowExceptions()
    {
        foreach ($this->supportedEncodings as $value) {
            $escaper = new Escaper($value);
        }
    }

    /**
     * @expectedException \Zend\Escaper\Exception\InvalidArgumentException
     */
    public function testSettingEncodingToInvalidValueShouldThrowException()
    {
        $escaper = new Escaper('invalid-encoding');
    }

    public function testReturnsEncodingFromGetter()
    {
        $this->assertEquals('UTF-8', $this->escaper->getEncoding());
    }

    public function testHtmlEscapingConvertsSpecialChars()
    {
        foreach ($this->htmlSpecialChars as $key => $value) {
            $this->assertEquals($value, $this->escaper->escapeHtml($key));
        }
    }

    public function testJavascriptEscapingConvertsSpecialChars()
    {
        foreach ($this->jsSpecialChars as $key => $value) {
            $this->assertEquals($value, $this->escaper->escapeJs($key), 'Failed to escape: '
                .$key);
        }
    }

    public function testJavascriptEscapingReturnsStringIfZeroLength()
    {
        $this->assertEquals('', $this->escaper->escapeJs(''));
    }

    public function testJavascriptEscapingReturnsStringIfContainsOnlyDigits()
    {
        $this->assertEquals('123', $this->escaper->escapeJs('123'));
    }

    public function testCssEscapingConvertsSpecialChars()
    {
        foreach ($this->cssSpecialChars as $key => $value) {
            $this->assertEquals($value, $this->escaper->escapeCss($key), 'Failed to escape: '
                .$key);
        }
    }

    public function testCssEscapingReturnsStringIfZeroLength()
    {
        $this->assertEquals('', $this->escaper->escapeCss(''));
    }

    public function testCssEscapingReturnsStringIfContainsOnlyDigits()
    {
        $this->assertEquals('123', $this->escaper->escapeCss('123'));
    }

    public function testUrlEscapingConvertsSpecialChars()
    {
        foreach ($this->urlSpecialChars as $key => $value) {
            $this->assertEquals($value, $this->escaper->escapeUrl($key), 'Failed to escape: '
                .$key);
        }
    }
}
