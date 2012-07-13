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
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\Stdlib;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Stdlib\StringUtils;

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

    public function singleByCharsets()
    {
        return array(
            array('AscII'),
            array('ISo-8859-1'),
        );
    }

    public function nonSingleByteCharsets()
    {
        return array(
            array('UTf-8'),
            array('usC-2')
        );
    }

    /**
     * @dataProvider singleByCharsets
     * @param string $charset
     */
    public function testIsSingleByteCharsetReturnsTrue($charset)
    {
        $this->assertTrue(StringUtils::isSingleByteCharset($charset));
    }

    /**
     * @dataProvider nonSingleByteCharsets
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
            if (extension_loaded('mbstring') || extension_loaded('iconv')
            ) {
                $this->fail("Failed to get mbstring or iconv wrapper for UTF-8 and ISO-8859-1");
            }
        }
    }
}
