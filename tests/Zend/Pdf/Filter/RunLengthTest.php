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
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

