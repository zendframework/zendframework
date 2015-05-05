<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mail\Header;

class DateTest extends TestCase
{
    public function headerLines()
    {
        return array(
            'newline'      => array("Date: xxx yyy\n"),
            'cr-lf'        => array("Date: xxx yyy\r\n"),
            'cr-lf-wsp'    => array("Date: xxx yyy\r\n\r\n"),
            'multiline'    => array("Date: xxx\r\ny\r\nyy"),
        );
    }

    /**
     * @dataProvider headerLines
     * @group ZF2015-04
     */
    public function testFromStringRaisesExceptionOnCrlfInjectionAttempt($header)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        Header\Date::fromString($header);
    }

    /**
     * @group ZF2015-04
     */
    public function testPreventsCRLFInjectionViaConstructor()
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $address = new Header\Date("This\ris\r\na\nCRLF Attack");
    }
}
