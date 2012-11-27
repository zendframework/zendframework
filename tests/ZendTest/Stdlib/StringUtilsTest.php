<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\StringUtils;

class StringUtilsTest extends TestCase
{

    protected $bufferedWrappers;

    public function setUp()
    {
        $this->bufferedWrappers = StringUtils::getRegisteredWrappers();
    }

    public function tearDown()
    {
        // reset registered wrappers
        foreach (StringUtils::getRegisteredWrappers() as $wrapper) {
            StringUtils::unregisterWrapper($wrapper);
        }
        foreach ($this->bufferedWrappers as $wrapper) {
            StringUtils::registerWrapper($wrapper);
        }

    }

    public function getSingleByCharsets()
    {
        return array(
            array('AscII'),
            array('7bit'),
            array('8bit'),
            array('ISo-8859-1'),
            array('ISo-8859-2'),
            array('ISo-8859-3'),
            array('ISo-8859-4'),
            array('ISo-8859-5'),
            array('ISo-8859-6'),
            array('ISo-8859-7'),
            array('ISo-8859-8'),
            array('ISo-8859-9'),
            array('ISo-8859-10'),
            array('ISo-8859-11'),
            array('ISo-8859-13'),
            array('ISo-8859-14'),
            array('ISo-8859-15'),
            array('ISo-8859-16'),
        );
    }

    /**
     * @dataProvider getSingleByCharsets
     * @param string $charset
     */
    public function testIsSingleByteCharsetReturnsTrue($charset)
    {
        $this->assertTrue(StringUtils::isSingleByteCharset($charset));
    }

    public function getNonSingleByteCharsets()
    {
        return array(
            array('UTf-8'),
            array('UTf-16'),
            array('usC-2'),
            array('CESU-8'),
        );
    }

    /**
     * @dataProvider getNonSingleByteCharsets
     * @param string $charset
     */
    public function testIsSingleByteCharsetReturnsFalse($charset)
    {
        $this->assertFalse(StringUtils::isSingleByteCharset($charset));
    }

    public function testGetWrapper()
    {
        $wrapper = StringUtils::getWrapper('ISO-8859-1');
        if (extension_loaded('mbstring')) {
            $this->assertInstanceOf('Zend\Stdlib\StringWrapper\MbString', $wrapper);
        } elseif (extension_loaded('iconv')) {
            $this->assertInstanceOf('Zend\Stdlib\StringWrapper\Iconv', $wrapper);
        } else {
            $this->assertInstanceOf('Zend\Stdlib\StringWrapper\Native', $wrapper);
        }

        try {
            $wrapper = StringUtils::getWrapper('UTF-8');
            if (extension_loaded('intl')) {
                $this->assertInstanceOf('Zend\Stdlib\StringWrapper\Intl', $wrapper);
            } elseif (extension_loaded('mbstring')) {
                $this->assertInstanceOf('Zend\Stdlib\StringWrapper\MbString', $wrapper);
            } elseif (extension_loaded('iconv')) {
                $this->assertInstanceOf('Zend\Stdlib\StringWrapper\Iconv', $wrapper);
            }
        } catch (Exception $e) {
            if (extension_loaded('intl')
                || extension_loaded('mbstring')
                || extension_loaded('iconv')
            ) {
                $this->fail("Failed to get intl, mbstring or iconv wrapper for UTF-8");
            }
        }

        try {
            $wrapper = StringUtils::getWrapper('UTF-8', 'ISO-8859-1');
            if (extension_loaded('mbstring')) {
                $this->assertInstanceOf('Zend\Stdlib\StringWrapper\MbString', $wrapper);
            } elseif (extension_loaded('iconv')) {
                $this->assertInstanceOf('Zend\Stdlib\StringWrapper\Iconv', $wrapper);
            }
        } catch (Exception $e) {
            if (extension_loaded('mbstring') || extension_loaded('iconv')) {
                $this->fail("Failed to get mbstring or iconv wrapper for UTF-8 and ISO-8859-1");
            }
        }
    }

    public function getUtf8StringValidity()
    {
        return array(
            // valid
            array('', true),
            array("\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F"
                . "\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F"
                . ' !"#$%&\'()*+,-./0123456789:;<=>?'
                . '@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_'
                . '`abcdefghijklmnopqrstuvwxyz{|}~',
                true
            ),

            // invalid
            array(true, false),
            array(123, false),
            array(123.45, false),
            array("\xFF", false),
            array("\x90a", false),
        );
    }

    /**
     * @dataProvider getUtf8StringValidity
     * @param string $str
     * @param boolean $valid
     */
    public function testIsValidUtf8($str, $valid)
    {
        $this->assertSame($valid, StringUtils::isValidUtf8($str));
    }
}
