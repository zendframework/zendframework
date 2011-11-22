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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mail;

use Zend\Mail\Address,
    Zend\Mail\AddressList,
    Zend\Mail\Message;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->message = new Message();
    }

    public function testInvalidByDefault()
    {
        $this->assertFalse($this->message->isValid());
    }

    public function testSetsOrigDateHeaderByDefault()
    {
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('orig-date'));
        $header  = $headers->get('orig-date');
        $date    = date('r');
        $date    = substr($date, 0, 16);
        $test    = $header->getFieldValue();
        $test    = substr($test, 0, 16);
        $this->assertEquals($date, $test);
    }

    public function testAddingFromAddressMarksAsValid()
    {
        $this->message->addFrom('zf-devteam@zend.com');
        $this->assertTrue($this->message->isValid());
    }

    public function testHeadersMethodReturnsHeadersObject()
    {
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
    }

    public function testToMethodReturnsAddressListObject()
    {
        $this->message->addTo('zf-devteam@zend.com');
        $to = $this->message->to();
        $this->assertInstanceOf('Zend\Mail\AddressList', $to);
    }

    public function testToAddressListLivesInHeaders()
    {
        $this->message->addTo('zf-devteam@zend.com');
        $to      = $this->message->to();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('to'));
        $header  = $headers->get('to');
        $this->assertSame($header->getAddressList(), $to);
    }

    public function testFromMethodReturnsAddressListObject()
    {
        $this->message->addFrom('zf-devteam@zend.com');
        $from = $this->message->from();
        $this->assertInstanceOf('Zend\Mail\AddressList', $from);
    }

    public function testFromAddressListLivesInHeaders()
    {
        $this->message->addFrom('zf-devteam@zend.com');
        $from    = $this->message->from();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('from'));
        $header  = $headers->get('from');
        $this->assertSame($header->getAddressList(), $from);
    }

    public function testCcMethodReturnsAddressListObject()
    {
        $this->message->addCc('zf-devteam@zend.com');
        $cc = $this->message->cc();
        $this->assertInstanceOf('Zend\Mail\AddressList', $cc);
    }

    public function testCcAddressListLivesInHeaders()
    {
        $this->message->addCc('zf-devteam@zend.com');
        $cc      = $this->message->cc();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('cc'));
        $header  = $headers->get('cc');
        $this->assertSame($header->getAddressList(), $cc);
    }

    public function testBccMethodReturnsAddressListObject()
    {
        $this->message->addBcc('zf-devteam@zend.com');
        $bcc = $this->message->bcc();
        $this->assertInstanceOf('Zend\Mail\AddressList', $bcc);
    }

    public function testBccAddressListLivesInHeaders()
    {
        $this->message->addBcc('zf-devteam@zend.com');
        $bcc     = $this->message->bcc();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('bcc'));
        $header  = $headers->get('bcc');
        $this->assertSame($header->getAddressList(), $bcc);
    }

    public function testReplyToMethodReturnsAddressListObject()
    {
        $this->message->addReplyTo('zf-devteam@zend.com');
        $replyTo = $this->message->replyTo();
        $this->assertInstanceOf('Zend\Mail\AddressList', $replyTo);
    }

    public function testReplyToAddressListLivesInHeaders()
    {
        $this->message->addReplyTo('zf-devteam@zend.com');
        $replyTo = $this->message->replyTo();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('reply-to'));
        $header  = $headers->get('reply-to');
        $this->assertSame($header->getAddressList(), $replyTo);
    }

    public function testSenderIsNullByDefault()
    {
        $this->assertNull($this->message->getSender());
    }

    public function testSettingSenderCreatesAddressObject()
    {
        $this->message->setSender('zf-devteam@zend.com');
        $sender = $this->message->getSender();
        $this->assertInstanceOf('Zend\Mail\Address', $sender);
    }

    public function testCanSpecifyNameWhenSettingSender()
    {
        $this->message->setSender('zf-devteam@zend.com', 'ZF DevTeam');
        $sender = $this->message->getSender();
        $this->assertInstanceOf('Zend\Mail\Address', $sender);
        $this->assertEquals('ZF DevTeam', $sender->getName());
    }

    public function testCanProvideAddressObjectWhenSettingSender()
    {
        $sender = new Address('zf-devteam@zend.com');
        $this->message->setSender($sender);
        $test = $this->message->getSender();
        $this->assertSame($sender, $test);
    }

    public function testCanAddFromAddressUsingName()
    {
        $this->message->addFrom('zf-devteam@zend.com', 'ZF DevTeam');
        $addresses = $this->message->from();
        $this->assertEquals(1, count($addresses));
        $address = $addresses->current();
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertEquals('ZF DevTeam', $address->getName());
    }

    public function testCanAddFromAddressUsingAddressObject()
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->message->addFrom($address);

        $addresses = $this->message->from();
        $this->assertEquals(1, count($addresses));
        $test = $addresses->current();
        $this->assertSame($address, $test);
    }

    public function testCanAddManyFromAddressesUsingArray()
    {
        $addresses = array(
            'zf-devteam@zend.com',
            'ZF Contributors List' => 'zf-contributors@lists.zend.com',
            new Address('fw-announce@lists.zend.com', 'ZF Announce List'),
        );
        $this->message->addFrom($addresses);

        $from = $this->message->from();
        $this->assertEquals(3, count($from));

        $this->assertTrue($from->has('zf-devteam@zend.com'));
        $this->assertTrue($from->has('zf-contributors@lists.zend.com'));
        $this->assertTrue($from->has('fw-announce@lists.zend.com'));
    }

    public function testCanAddManyFromAddressesUsingAddressListObject()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addFrom('fw-announce@lists.zend.com');
        $this->message->addFrom($list);
        $from = $this->message->from();
        $this->assertEquals(2, count($from));
        $this->assertTrue($from->has('fw-announce@lists.zend.com'));
        $this->assertTrue($from->has('zf-devteam@zend.com'));
    }

    public function testCanSetFromListFromAddressList()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addFrom('fw-announce@lists.zend.com');
        $this->message->setFrom($list);
        $from = $this->message->from();
        $this->assertEquals(1, count($from));
        $this->assertFalse($from->has('fw-announce@lists.zend.com'));
        $this->assertTrue($from->has('zf-devteam@zend.com'));
    }

    public function testCanAddCcAddressUsingName()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddCcAddressUsingAddressObject()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddManyCcAddressesUsingArray()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddManyCcAddressesUsingAddressListObject()
    {
        $this->markTestIncomplete();
    }

    public function testCanSetCcListFromAddressList()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddBccAddressUsingName()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddBccAddressUsingAddressObject()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddManyBccAddressesUsingArray()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddManyBccAddressesUsingAddressListObject()
    {
        $this->markTestIncomplete();
    }

    public function testCanSetBccListFromAddressList()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddReplyToAddressUsingName()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddReplyToAddressUsingAddressObject()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddManyReplyToAddressesUsingArray()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddManyReplyToAddressesUsingAddressListObject()
    {
        $this->markTestIncomplete();
    }

    public function testCanSetReplyToListFromAddressList()
    {
        $this->markTestIncomplete();
    }

    public function testSubjectIsEmptyByDefault()
    {
        $this->assertNull($this->message->getSubject());
    }

    public function testSubjectIsMutable()
    {
        $this->message->setSubject('test subject');
        $subject = $this->message->getSubject();
        $this->assertEquals('test subject', $subject);
    }

    public function testSettingSubjectProxiesToHeader()
    {
        $this->message->setSubject('test subject');
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('subject'));
        $header = $headers->get('subject');
        $this->assertEquals('test subject', $header->getFieldValue());
    }

    public function testBodyIsEmptyByDefault()
    {
        $this->assertNull($this->message->getBody());
    }

    public function testMaySetBodyFromString()
    {
        $this->message->setBody('body');
        $this->assertEquals('body', $this->message->getBody());
    }

    public function testMaySetBodyFromStringSerializableObject()
    {
        $object = new TestAsset\StringSerializableObject('body');
        $this->message->setBody($object);
        $this->assertSame($object, $this->message->getBody());
        $this->assertEquals('body', $this->message->getBodyText());
    }

    public function testMaySetBodyFromMimeMessage()
    {
        $this->markTestIncomplete();
    }

    public function testSettingBodyFromSinglePartMimeMessageSetsAppropriateHeaders()
    {
        $this->markTestIncomplete();
        // test content-type
    }

    public function testSettingBodyFromMultiPartMimeMessageSetsAppropriateHeaders()
    {
        $this->markTestIncomplete();
        // test content-type, boundary
    }

    public function testRetrievingBodyTextFromMessageWithMultiPartMimeBodyReturnsMimeSerialization()
    {
        $this->markTestIncomplete();
    }
}
