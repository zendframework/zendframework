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
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Pdf_Filter_Ascii85
 */

/**
 * PHPUnit Test Case
 */

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_Filter_Ascii85Test extends PHPUnit_Framework_TestCase
{
    public function testStringDivisibleBy4Encode()
    {
        $decodedContents = "Lorem ipsum dolor sit amet orci aliquam.";
        $encodedContents = Zend_Pdf_Filter_Ascii85::encode($decodedContents);
        $testString = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATT&5Earf+@;KXtF^],>~>";
        $this->assertEquals($encodedContents, $testString);
    }

    public function testStringDivisibleBy4Decode()
    {
        $encodedContents = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATT&5Earf+@;KXtF^],>~>";
        $decodedContents = Zend_Pdf_Filter_Ascii85::decode($encodedContents);
        $testString = 'Lorem ipsum dolor sit amet orci aliquam.';
        $this->assertEquals($decodedContents, $testString);
    }

    public function testStringNotDivisibleBy4Encode()
    {
        $decodedContents = 'Lorem ipsum dolor sit amet, consectetur cras amet.';
        $encodedContents = Zend_Pdf_Filter_Ascii85::encode($decodedContents);
        $testString  = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATTIG@rH7+ARfgn"
                     . "FEMUH@rc\"!+CT+uF=m~>";
        $this->assertEquals($encodedContents, $testString);
    }

    public function testStringNotDivisibleBy4Decode()
    {
        $encodedContents = "9Q+r_D'3P3F*2=BA8c:&EZfF;F<G\"/ATTIG@rH7+ARfgn"
                         . "FEMUH@rc\"!+CT+uF=m~>";
        $decodedContents = Zend_Pdf_Filter_Ascii85::decode($encodedContents);
        $testString = 'Lorem ipsum dolor sit amet, consectetur cras amet.';
        $this->assertEquals($decodedContents, $testString);
    }
}
