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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf\Value;
use Zend\Amf\Value;

/**
 * Test case for Zend_Amf_Value_MessageHeader
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class MessageHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorShouldSetMessageHeaderName()
    {
        $messageHeader = new Value\MessageHeader('foo', true, 'content');
        $this->assertEquals('foo', $messageHeader->name);
    }

    public function testConstructorShouldSetMessageHeaderMustReadFlag()
    {
        $messageHeader = new Value\MessageHeader('foo', true, 'content');
        $this->assertTrue($messageHeader->mustRead);
        $messageHeader = new Value\MessageHeader('foo', false, 'content');
        $this->assertFalse($messageHeader->mustRead);
    }

    public function testConstructorShouldCastMessageHeaderMustReadFlagToBoolean()
    {
        $messageHeader = new Value\MessageHeader('foo', 'foo', 'content');
        $this->assertTrue($messageHeader->mustRead);
        $messageHeader = new Value\MessageHeader('foo', 0, 'content');
        $this->assertFalse($messageHeader->mustRead);
    }

    public function testConstructorShouldSetMessageHeaderDataUnmodified()
    {
        $data = new \stdClass;
        $data->foo = 'bar';
        $data->bar = array('baz' => 'bat');
        $messageHeader = new Value\MessageHeader('foo', true, $data);
        $this->assertSame($data, $messageHeader->data);
    }

    public function testConstructorShouldNotSetLengthIfNotProvided()
    {
        $messageHeader = new Value\MessageHeader('foo', true, 'content');
        $this->assertNull($messageHeader->length);
    }

    public function testConstructorShouldCastLengthToInteger()
    {
        $messageHeader = new Value\MessageHeader('foo', 'foo', 'content', '2');
        $this->assertSame(2, $messageHeader->length);
    }
}
