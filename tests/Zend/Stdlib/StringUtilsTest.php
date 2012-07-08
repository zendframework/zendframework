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

    protected $bufferedAdapters;

    public function setUp()
    {
        $this->bufferedAdapters = StringUtils::getRegisteredAdapters();
    }

    public function tearDown()
    {
        // reset registered adapters
        foreach (StringUtils::getRegisteredAdapters() as $adapter) {
            StringUtils::unregisterAdapter($adapter);
        }
        foreach ($this->bufferedAdapters as $adapter) {
            StringUtils::registerAdapter($adapter);
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

    public function testGetAdapterByCharset()
    {
        $adapter = StringUtils::getAdapterByCharset('UTF-8');

        if (extension_loaded('mbstring')) {
            $this->assertInstanceOf('Zend\Stdlib\StringAdapter\MbString', $adapter);
        } elseif (extension_loaded('iconv')) {
            $this->assertInstanceOf('Zend\Stdlib\StringAdapter\Iconv', $adapter);
        } else {
            $this->assertInstanceOf('Zend\Stdlib\StringAdapter\Native', $adapter);
        }
    }
}
