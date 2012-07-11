<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendTest\Pdf\InternalType;

use Zend\Pdf\InternalType;


/**
 * \Zend\Pdf\InternalType\StringObject
 */

/**
 * PHPUnit Test Case
 */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @group      Zend_PDF
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testPDFString()
    {
        $stringObj = new InternalType\StringObject('some text');
        $this->assertTrue($stringObj instanceof InternalType\StringObject);
    }

    public function testGetType()
    {
        $stringObj = new InternalType\StringObject('some text');
        $this->assertEquals($stringObj->getType(), InternalType\AbstractTypeObject::TYPE_STRING);
    }

    public function testToString()
    {
        $stringObj = new InternalType\StringObject('some text ()');
        $this->assertEquals($stringObj->toString(), '(some text \\(\\))' );
    }

    public function testEscape()
    {
        $this->assertEquals(InternalType\StringObject::escape("\n\r\t\x08\x0C()\\"), "\\n\\r\\t\\b\\f\\(\\)\\\\");
    }

    public function testUnescape()
    {
        $this->assertEquals(InternalType\StringObject::unescape("\\n\\r\\t\\b\\f\\(\\)\\\\  \nsome \\\ntext"),
                            "\n\r\t\x08\x0C()\\  \nsome text");
    }

    /**
     * @group ZF-9450
     */
    public function testUnescapeOctal()
    {
        $input = array(
            0304 => '\\304',
            0326 => '\\326',
            0334 => '\\334'
        );
        foreach ($input as $k => $v) {
            $this->assertEquals(InternalType\StringObject::unescape($v),
                chr($k), 'expected German Umlaut');
        }
    }
}
