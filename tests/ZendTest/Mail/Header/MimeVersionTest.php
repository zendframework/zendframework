<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Header;

/**
 * @group      Zend_Mail
 */
class MimeVersionTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingManually()
    {
        $version = "2.0";
        $mime = new Header\MimeVersion();
        $mime->setVersion($version);
        $this->assertEquals($version, $mime->getFieldValue());
    }

    public function testDefaultVersion()
    {
        $mime = new Header\MimeVersion();
        $this->assertEquals('1.0', $mime->getVersion());
    }

    public function headerLines()
    {
        return array(
            'newline'      => array("MIME-Version: 5.0\nbar"),
            'cr-lf'        => array("MIME-Version: 2.0\r\n"),
            'cr-lf-wsp'    => array("MIME-Version: 3\r\n\r\n.1"),
            'multiline'    => array("MIME-Version: baz\r\nbar\r\nbau"),
        );
    }

    /**
     * @dataProvider headerLines
     * @group ZF2015-04
     */
    public function testFromStringRaisesExceptionOnDetectionOfCrlfInjection($header)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $mime = Header\MimeVersion::fromString($header);
    }

    public function invalidVersions()
    {
        return array(
            'no-decimal'    => array('1'),
            'multi-decimal' => array('1.0.0'),
            'alpha'         => array('X.Y'),
            'non-alnum'     => array('Version 1.0'),
        );
    }

    /**
     * @dataProvider invalidVersions
     * @group ZF2015-04
     */
    public function testRaisesExceptionOnInvalidVersionFromSetVersion($value)
    {
        $header = new Header\MimeVersion();
        $this->setExpectedException('Zend\Mail\Header\Exception\InvalidArgumentException');
        $header->setVersion($value);
    }
}
