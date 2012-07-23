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
 * \Zend\Pdf\Filter\RunLength
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
class RunLengthTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleStringEncode()
    {
        $decodedContents  = 'WWWWWWWWWWWWBWWWWWWWWWWWWBBBWWWW'
                          . 'WWWWWWWWWWWWWWWWWWWWBWWWWWWWWWWWWWW';
        $encodedContents = StreamFilter\RunLength::encode($decodedContents);
        $testString  = "\xF5W\x00B\xF5W\xFEB\xE9W\x00B\xF3W\x80";
        $this->assertEquals($encodedContents, $testString);
    }

    public function testSimpleStringDecode()
    {
        $encodedContents  = "\xF5W\x00B\xF5W\xFEB\xE9W\x00B\xF3W\x80";
        $decodedContents = StreamFilter\RunLength::decode($encodedContents);
        $testString  = 'WWWWWWWWWWWWBWWWWWWWWWWWWBBBWWWW'
                     . 'WWWWWWWWWWWWWWWWWWWWBWWWWWWWWWWWWWW';
        $this->assertEquals($decodedContents, $testString);
    }

    public function testRepeatBytesLongerThan128BytesEncode()
    {
        $decodedContents  = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                          . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                          . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                          . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                          . 'AAAAAAAAAAAAAAAAAAAAAABBBCDEFFFF';
        $encodedContents = StreamFilter\RunLength::encode($decodedContents);
        $testString  = "\x81A\xEBA\xFEB\x02CDE\xFDF\x80";
        $this->assertEquals($encodedContents, $testString);
    }

    public function testRepeatBytesLongerThan128BytesDecode()
    {
        $testString  = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                     . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                     . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                     . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
                     . 'AAAAAAAAAAAAAAAAAAAAAABBBCDEFFFF';

        $encodedContents = "\x81A\xEBA\xFEB\x00C\x00D\x00E\xFDF\x80";
        $this->assertEquals(StreamFilter\RunLength::decode($encodedContents), $testString);

        $encodedContents = "\x81A\xEBA\xFEB\x02CDE\xFDF\x80";
        $this->assertEquals(StreamFilter\RunLength::decode($encodedContents), $testString);
    }
}

