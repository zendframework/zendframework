<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendTest\Pdf\Filter;

use Zend\Pdf\InternalType\StreamFilter;

/**
 * \Zend\Pdf\Filter\Ascii85
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
class Ascii85Test extends \PHPUnit_Framework_TestCase
{
    public function testStringDivisibleBy4Encode()
    {
        $decodedContents = "Lorem ipsum dolor sit amet orci aliquam.";
        $encodedContents = StreamFilter\Ascii85::encode($decodedContents);
        $testString = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATT&5Earf+@;KXtF^],>~>";
        $this->assertEquals($encodedContents, $testString);
    }

    public function testStringDivisibleBy4Decode()
    {
        $encodedContents = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATT&5Earf+@;KXtF^],>~>";
        $decodedContents = StreamFilter\Ascii85::decode($encodedContents);
        $testString = 'Lorem ipsum dolor sit amet orci aliquam.';
        $this->assertEquals($decodedContents, $testString);
    }

    public function testStringNotDivisibleBy4Encode()
    {
        $decodedContents = 'Lorem ipsum dolor sit amet, consectetur cras amet.';
        $encodedContents = StreamFilter\Ascii85::encode($decodedContents);
        $testString  = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATTIG@rH7+ARfgn"
                     . "FEMUH@rc\"!+CT+uF=m~>";
        $this->assertEquals($encodedContents, $testString);
    }

    public function testStringNotDivisibleBy4Decode()
    {
        $encodedContents = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATTIG@rH7+ARfgn"
                         . "FEMUH@rc\"!+CT+uF=m~>";
        $decodedContents = StreamFilter\Ascii85::decode($encodedContents);
        $testString = 'Lorem ipsum dolor sit amet, consectetur cras amet.';
        $this->assertEquals($decodedContents, $testString);
    }
}
