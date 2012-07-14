<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\Value;

use Zend\Amf\Value;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
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
