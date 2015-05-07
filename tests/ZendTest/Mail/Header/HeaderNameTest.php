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
use Zend\Mail\Header\HeaderName;

class HeaderNameTest extends TestCase
{
    /**
     * Data for filter name
     */
    public function getFilterNames()
    {
        return array(
            array('Subject', 'Subject'),
            array('Subject:', 'Subject'),
            array(':Subject:', 'Subject'),
            array('Subject' . chr(32), 'Subject'),
            array('Subject' . chr(33), 'Subject' . chr(33)),
            array('Subject' . chr(126), 'Subject' . chr(126)),
            array('Subject' . chr(127), 'Subject'),
        );
    }

    /**
     * @dataProvider getFilterNames
     * @group ZF2015-04
     */
    public function testFilterName($name, $expected)
    {
        $this->assertEquals($expected, HeaderName::filter($name));
    }

    public function validateNames()
    {
        return array(
            array('Subject', 'assertTrue'),
            array('Subject:', 'assertFalse'),
            array(':Subject:', 'assertFalse'),
            array('Subject' . chr(32), 'assertFalse'),
            array('Subject' . chr(33), 'assertTrue'),
            array('Subject' . chr(126), 'assertTrue'),
            array('Subject' . chr(127), 'assertFalse'),
        );
    }

    /**
     * @dataProvider validateNames
     * @group ZF2015-04
     */
    public function testValidateName($name, $assertion)
    {
        $this->{$assertion}(HeaderName::isValid($name));
    }

    public function assertNames()
    {
        return array(
            array('Subject:'),
            array(':Subject:'),
            array('Subject' . chr(32)),
            array('Subject' . chr(127)),
        );
    }

    /**
     * @dataProvider assertNames
     * @group ZF2015-04
     */
    public function testAssertValidRaisesExceptionForInvalidNames($name)
    {
        $this->setExpectedException('Zend\Mail\Header\Exception\RuntimeException', 'Invalid');
        HeaderName::assertValid($name);
    }
}
