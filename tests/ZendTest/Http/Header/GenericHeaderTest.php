<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Exception\InvalidArgumentException;
use Zend\Http\Header\GenericHeader;
use PHPUnit_Framework_TestCase as TestCase;

class GenericHeaderTest extends TestCase
{
    /**
     * @param string $name
     * @dataProvider validFieldNameChars
     */
    public function testValidFieldName($name)
    {
        try {
            new GenericHeader($name);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                 $e->getMessage(),
                     'Header name must be a valid RFC 2616 (section 4.2) field-name.'
            );
            $this->fail('Allowed char rejected: ' . ord($name)); // For easy debug
        }
    }

    /**
     * @param string $name
     * @dataProvider invalidFieldNameChars
     */
    public function testInvalidFieldName($name)
    {
        try {
            new GenericHeader($name);
            $this->fail('Invalid char allowed: ' . ord($name)); // For easy debug
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                 $e->getMessage(),
                     'Header name must be a valid RFC 2616 (section 4.2) field-name.'
            );
        }
    }

    /**
     * Valid field name characters.
     *
     * @return string[]
     */
    public function validFieldNameChars()
    {
        return array(
            array('!'),
            array('#'),
            array('$'),
            array('%'),
            array('&'),
            array("'"),
            array('*'),
            array('+'),
            array('-'),
            array('.'),
            array('0'), // Begin numeric range
            array('9'), // End numeric range
            array('A'), // Begin upper range
            array('Z'), // End upper range
            array('^'),
            array('_'),
            array('`'),
            array('a'), // Begin lower range
            array('z'), // End lower range
            array('|'),
            array('~'),
        );
    }

    /**
     * Invalid field name characters.
     *
     * @return string[]
     */
    public function invalidFieldNameChars()
    {
        return array(
            array("\x00"), // Min CTL invalid character range.
            array("\x1F"), // Max CTL invalid character range.
            array('('),
            array(')'),
            array('<'),
            array('>'),
            array('@'),
            array(','),
            array(';'),
            array(':'),
            array('\\'),
            array('"'),
            array('/'),
            array('['),
            array(']'),
            array('?'),
            array('='),
            array('{'),
            array('}'),
            array(' '),
            array("\t"),
            array("\x7F"), // DEL CTL invalid character.
        );
    }
}
