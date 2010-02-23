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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * Zend_Mail
 */
require_once 'Zend/Mail.php';

/**
 * Zend_Mail_Transport_Abstract
 */
require_once 'Zend/Mail/Transport/Abstract.php';

/**
 * Zend_Mail_Transport_Sendmail
 */
require_once 'Zend/Mail/Transport/Sendmail.php';

/**
 * Zend_Mail_Transport_Smtp
 */
require_once 'Zend/Mail/Transport/Smtp.php';

/**
 * Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Mock mail transport class for testing purposes
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_Mock extends Zend_Mail_Transport_Abstract
{
    /**
     * @var Zend_Mail
     */
    public $mail       = null;
    public $returnPath = null;
    public $subject    = null;
    public $from       = null;
    public $headers    = null;
    public $called     = false;

    public function _sendMail()
    {
        $this->mail       = $this->_mail;
        $this->subject    = $this->_mail->getSubject();
        $this->from       = $this->_mail->getFrom();
        $this->returnPath = $this->_mail->getReturnPath();
        $this->headers    = $this->_headers;
        $this->called     = true;
    }
}

/**
 * Mock mail transport class for testing Sendmail transport
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_Sendmail_Mock extends Zend_Mail_Transport_Sendmail
{
    /**
     * @var Zend_Mail
     */
    public $mail    = null;
    public $from    = null;
    public $subject = null;
    public $called  = false;

    public function _sendMail()
    {
        $this->mail    = $this->_mail;
        $this->from    = $this->_mail->getFrom();
        $this->subject = $this->_mail->getSubject();
        $this->called  = true;
    }
}

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class Zend_Mail_MailTest extends PHPUnit_Framework_TestCase
{

    public function tearDown() {
        Zend_Mail::clearDefaultFrom();
        Zend_Mail::clearDefaultReplyTo();
    }

    /**
     * Test case for a simple email text message with
     * multiple recipients.
     *
     */
    public function testOnlyText()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('This is a test.');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('My Subject');
        $mail->addTo('recipient1@example.com');
        $mail->addTo('recipient2@example.com');
        $mail->addBcc('recipient1_bcc@example.com');
        $mail->addBcc('recipient2_bcc@example.com');
        $mail->addCc('recipient1_cc@example.com', 'Example no. 1 for cc');
        $mail->addCc('recipient2_cc@example.com', 'Example no. 2 for cc');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertEquals('My Subject', $mock->subject);
        $this->assertEquals('testmail@example.com', $mock->from);
        $this->assertContains('recipient1@example.com', $mock->recipients);
        $this->assertContains('recipient2@example.com', $mock->recipients);
        $this->assertContains('recipient1_bcc@example.com', $mock->recipients);
        $this->assertContains('recipient2_bcc@example.com', $mock->recipients);
        $this->assertContains('recipient1_cc@example.com', $mock->recipients);
        $this->assertContains('recipient2_cc@example.com', $mock->recipients);
        $this->assertContains('This is a test.', $mock->body);
        $this->assertContains('Content-Transfer-Encoding: quoted-printable', $mock->header);
        $this->assertContains('Content-Type: text/plain', $mock->header);
        $this->assertContains('From: test Mail User <testmail@example.com>', $mock->header);
        $this->assertContains('Subject: My Subject', $mock->header);
        $this->assertContains('To: recipient1@example.com', $mock->header);
        $this->assertContains('Cc: Example no. 1 for cc <recipient1_cc@example.com>', $mock->header);
    }

    /**
     * Test sending in arrays of recipients
     */
    public function testArrayRecipients()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('Test #2');
        $mail->setFrom('eli@example.com', 'test Mail User');
        $mail->setSubject('Subject #2');
        $mail->addTo(array('heather@example.com', 'Ramsey White' => 'ramsey@example.com'));
        $mail->addCc(array('keith@example.com', 'Cal Evans' => 'cal@example.com'));
        $mail->addBcc(array('ralph@example.com', 'matthew@example.com'));

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertEquals('eli@example.com', $mock->from);
        $this->assertContains('heather@example.com', $mock->recipients);
        $this->assertContains('ramsey@example.com', $mock->recipients);
        $this->assertContains('ralph@example.com', $mock->recipients);
        $this->assertContains('matthew@example.com', $mock->recipients);
        $this->assertContains('keith@example.com', $mock->recipients);
        $this->assertContains('cal@example.com', $mock->recipients);
        $this->assertContains('Test #2', $mock->body);
        $this->assertContains('From: test Mail User <eli@example.com>', $mock->header);
        $this->assertContains('Subject: Subject #2', $mock->header);
        $this->assertContains('To: heather@example.com', $mock->header);
        $this->assertContains('Ramsey White <ramsey@example.com>', $mock->header);
        $this->assertContains('Cal Evans <cal@example.com>', $mock->header);
    }

    /**
     * @group ZF-8503 Test recipients Header format.
     */
    public function testRecipientsHeaderFormat()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('Test recipients Header format.');
        $mail->setFrom('yoshida@example.com', 'test Mail User');
        $mail->setSubject('Test recipients Header format.');
        $mail->addTo('address_to1@example.com', 'name_to@example.com');
        $mail->addTo('address_to2@example.com', 'noinclude comma nor at mark');
        $mail->addCc('address_cc@example.com', 'include, name_cc');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertEquals('yoshida@example.com', $mock->from);
        $this->assertContains('Test recipients Header format.', $mock->body);
        $this->assertContains('To: "name_to@example.com" <address_to1@example.com>', $mock->header);
        $this->assertContains('noinclude comma nor at mark <address_to2@example.com>', $mock->header);
        $this->assertContains('Cc: "include, name_cc" <address_cc@example.com>', $mock->header);
    }

    /**
     * Check if Header Fields are encoded correctly and if
     * header injection is prevented.
     */
    public function testHeaderEncoding()
    {
        $mail = new Zend_Mail("UTF-8");
        $mail->setBodyText('My Nice Test Text');
        // try header injection:
        $mail->addTo("testmail@example.com\nCc:foobar@example.com");
        $mail->addHeader('X-MyTest', "Test\nCc:foobar2@example.com", true);
        // try special Chars in Header Fields:
        $mail->setFrom('mymail@example.com', "\xC6\x98\xC6\x90\xC3\xA4\xC4\xB8");
        $mail->addTo('testmail2@example.com', "\xC4\xA7\xC4\xAF\xC7\xAB");
        $mail->addCc('testmail3@example.com', "\xC7\xB6\xC7\xB7");
        $mail->setSubject("\xC7\xB1\xC7\xAE");
        $mail->addHeader('X-MyTest', "Test-\xC7\xB1", true);

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertContains(
            'From: =?UTF-8?Q?=C6=98=C6=90=C3=A4=C4=B8?=',
            $mock->header,
            "From: Header was encoded unexpectedly."
        );
        $this->assertContains(
            "Cc:foobar@example.com",
            $mock->header
        );
        $this->assertNotContains(
            "\nCc:foobar@example.com",
            $mock->header,
            "Injection into From: header is possible."
        );
        $this->assertContains(
            '=?UTF-8?Q?=C4=A7=C4=AF=C7=AB?= <testmail2@example.com>',
            $mock->header
        );
        $this->assertContains(
            'Cc: =?UTF-8?Q?=C7=B6=C7=B7?= <testmail3@example.com>',
            $mock->header
        );
        $this->assertContains(
            'Subject: =?UTF-8?Q?=C7=B1=C7=AE?=',
            $mock->header
        );
        $this->assertContains(
            'X-MyTest:',
            $mock->header
        );
        $this->assertNotContains(
            "\nCc:foobar2@example.com",
            $mock->header
        );
        $this->assertContains(
            '=?UTF-8?Q?Test-=C7=B1?=',
            $mock->header
        );
    }

    /**
     * @group ZF-7799
     */
    public function testHeaderSendMailTransportHaveNoRightTrim()
    {
        $mail = new Zend_Mail("UTF-8");
        $mail->setBodyText('My Nice Test Text');
        $mail->addTo("foobar@example.com");
        $mail->setSubject("hello world!");

        $transportMock = new Zend_Mail_Transport_Sendmail_Mock();
        $mail->send($transportMock);

        $this->assertEquals($transportMock->header, rtrim($transportMock->header));
    }

    /**
     * Check if Header Fields are stripped accordingly in sendmail transport;
     * also check for header injection
     * @todo Determine why this fails in Windows (testmail3@example.com example)
     */
    public function testHeaderEncoding2()
    {
        $mail = new Zend_Mail("UTF-8");
        $mail->setBodyText('My Nice Test Text');
        // try header injection:
        $mail->addTo("testmail@example.com\nCc:foobar@example.com");
        $mail->addHeader('X-MyTest', "Test\nCc:foobar2@example.com", true);
        // try special Chars in Header Fields:
        $mail->setFrom('mymail@example.com', "\xC6\x98\xC6\x90\xC3\xA4\xC4\xB8");
        $mail->addTo('testmail2@example.com', "\xC4\xA7\xC4\xAF\xC7\xAB");
        $mail->addCc('testmail3@example.com', "\xC7\xB6\xC7\xB7");
        $mail->setSubject("\xC7\xB1\xC7\xAE");
        $mail->addHeader('X-MyTest', "Test-\xC7\xB1", true);

        $mock = new Zend_Mail_Transport_Sendmail_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertContains(
            'From: =?UTF-8?Q?=C6=98=C6=90=C3=A4=C4=B8?=',
            $mock->header,
            "From: Header was encoded unexpectedly."
        );
        $this->assertNotContains(
            "\nCc:foobar@example.com",
            $mock->header,
            "Injection into From: header is possible."
        );
        // To is done by mail() not in headers
        $this->assertNotContains(
            'To: =?UTF-8?Q?=C4=A7=C4=AF=C7=AB?= <testmail2@example.com>',
            $mock->header
        );
        $this->assertContains(
            'Cc: =?UTF-8?Q?=C7=B6=C7=B7?= <testmail3@example.com>',
            $mock->header
        );
        // Subject is done by mail() not in headers
        $this->assertNotContains(
            'Subject: =?UTF-8?Q?=C7=B1=C7=AE?=',
            $mock->header
        );
        $this->assertContains(
            'X-MyTest:',
            $mock->header
        );
        $this->assertNotContains(
            "\nCc:foobar2@example.com",
            $mock->header
        );
        $this->assertContains(
            '=?UTF-8?Q?Test-=C7=B1?=',
            $mock->header
        );
    }

    /**
     * Check if Mails with HTML and Text Body are generated correctly.
     *
     */
    public function testMultipartAlternative()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        $mail->setBodyHtml('My Nice <b>Test</b> Text');
        $mail->addTo('testmail@example.com', 'Test Recipient');
        $mail->setFrom('mymail@example.com', 'Test Sender');
        $mail->setSubject('Test: Alternate Mail with Zend_Mail');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        // check headers
        $this->assertTrue($mock->called);
        $this->assertContains('multipart/alternative', $mock->header);
        $boundary = $mock->boundary;
        $this->assertContains('boundary="' . $boundary . '"', $mock->header);
        $this->assertContains('MIME-Version: 1.0', $mock->header);

        // check body
        // search for first boundary
        $p1 = strpos($mock->body, "--$boundary\n");
        $this->assertNotNull($p1, $boundary . ': ' . $mock->body);

        // cut out first (Text) part
        $start1 = $p1 + 3 + strlen($boundary);
        $p2 = strpos($mock->body, "--$boundary\n", $start1);
        $this->assertNotNull($p2);

        $partBody1 = substr($mock->body, $start1, ($p2 - $start1));
        $this->assertContains('Content-Type: text/plain', $partBody1);
        $this->assertContains('My Nice Test Text', $partBody1);

        // check second (HTML) part
        // search for end boundary
        $start2 = $p2 + 3 + strlen($boundary);
        $p3 = strpos($mock->body, "--$boundary--");
        $this->assertNotNull($p3);

        $partBody2 = substr($mock->body, $start2, ($p3 - $start2));
        $this->assertContains('Content-Type: text/html', $partBody2);
        $this->assertContains('My Nice <b>Test</b> Text', $partBody2);
    }

    /**
     * check if attachment handling works
     *
     */
    public function testAttachment()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        $mail->addTo('testmail@example.com', 'Test Recipient');
        $mail->setFrom('mymail@example.com', 'Test Sender');
        $mail->setSubject('Test: Attachment Test with Zend_Mail');
        $at = $mail->createAttachment('abcdefghijklmnopqrstuvexyz');
        $at->type = 'image/gif';
        $at->id = 12;
        $at->filename = 'test.gif';
        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        // now check what was generated by Zend_Mail.
        // first the mail headers:
        $this->assertContains('Content-Type: multipart/mixed', $mock->header, $mock->header);
        $boundary = $mock->boundary;
        $this->assertContains('boundary="' . $boundary . '"', $mock->header);
        $this->assertContains('MIME-Version: 1.0', $mock->header);

        // check body
        // search for first boundary
        $p1 = strpos($mock->body, "--$boundary\n");
        $this->assertNotNull($p1);

        // cut out first (Text) part
        $start1 = $p1 + 3 + strlen($boundary);
        $p2 = strpos($mock->body, "--$boundary\n", $start1);
        $this->assertNotNull($p2);

        $partBody1 = substr($mock->body, $start1, ($p2 - $start1));
        $this->assertContains('Content-Type: text/plain', $partBody1);
        $this->assertContains('My Nice Test Text', $partBody1);

        // check second (HTML) part
        // search for end boundary
        $start2 = $p2 + 3 + strlen($boundary);
        $p3 = strpos($mock->body, "--$boundary--");
        $this->assertNotNull($p3);

        $partBody2 = substr($mock->body, $start2, ($p3 - $start2));
        $this->assertContains('Content-Type: image/gif', $partBody2);
        $this->assertContains('Content-Transfer-Encoding: base64', $partBody2);
        $this->assertContains('Content-ID: <12>', $partBody2);
    }

    /**
     * Check if Mails with HTML and Text Body are generated correctly.
     *
     */
    public function testMultipartAlternativePlusAttachment()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        $mail->setBodyHtml('My Nice <b>Test</b> Text');
        $mail->addTo('testmail@example.com', 'Test Recipient');
        $mail->setFrom('mymail@example.com', 'Test Sender');
        $mail->setSubject('Test: Alternate Mail with Zend_Mail');

        $at = $mail->createAttachment('abcdefghijklmnopqrstuvexyz');
        $at->type = 'image/gif';
        $at->id = 12;
        $at->filename = 'test.gif';

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        // check headers
        $this->assertTrue($mock->called);
        $this->assertContains('multipart/mixed', $mock->header);
        $boundary = $mock->boundary;
        $this->assertContains('boundary="' . $boundary . '"', $mock->header);
        $this->assertContains('MIME-Version: 1.0', $mock->header);

        // check body
        // search for first boundary
        $p1 = strpos($mock->body, "--$boundary\n");
        $this->assertNotNull($p1);

        // cut out first (multipart/alternative) part
        $start1 = $p1 + 3 + strlen($boundary);
        $p2 = strpos($mock->body, "--$boundary\n", $start1);
        $this->assertNotNull($p2);

        $partBody1 = substr($mock->body, $start1, ($p2 - $start1));
        $this->assertContains('Content-Type: multipart/alternative', $partBody1);
        $this->assertContains('Content-Type: text/plain', $partBody1);
        $this->assertContains('Content-Type: text/html', $partBody1);
        $this->assertContains('My Nice Test Text', $partBody1);
        $this->assertContains('My Nice <b>Test</b> Text', $partBody1);

        // check second (image) part
        // search for end boundary
        $start2 = $p2 + 3 + strlen($boundary);
        $p3 = strpos($mock->body, "--$boundary--");
        $this->assertNotNull($p3);

        $partBody2 = substr($mock->body, $start2, ($p3 - $start2));
        $this->assertContains('Content-Type: image/gif', $partBody2);
        $this->assertContains('Content-Transfer-Encoding: base64', $partBody2);
        $this->assertContains('Content-ID: <12>', $partBody2);
    }

    public function testReturnPath()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('This is a test.');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('My Subject');
        $mail->addTo('recipient1@example.com');
        $mail->addTo('recipient2@example.com');
        $mail->addBcc('recipient1_bcc@example.com');
        $mail->addBcc('recipient2_bcc@example.com');
        $mail->addCc('recipient1_cc@example.com', 'Example no. 1 for cc');
        $mail->addCc('recipient2_cc@example.com', 'Example no. 2 for cc');

        // First example: from and return-path should be equal
        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);
        $this->assertTrue($mock->called);
        $this->assertEquals($mail->getFrom(), $mock->returnPath);

        // Second example: from and return-path should not be equal
        $mail->setReturnPath('sender2@example.com');
        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);
        $this->assertTrue($mock->called);
        $this->assertNotEquals($mail->getFrom(), $mock->returnPath);
        $this->assertEquals($mail->getReturnPath(), $mock->returnPath);
        $this->assertNotEquals($mock->returnPath, $mock->from);
    }

    public function testNoBody()
    {
        $mail = new Zend_Mail();
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('My Subject');
        $mail->addTo('recipient1@example.com');

        // First example: from and return-path should be equal
        $mock = new Zend_Mail_Transport_Mock();
        try {
            $mail->send($mock);
            $this->assertTrue($mock->called);
        } catch (Exception $e) {
            // success
            $this->assertContains('No body specified', $e->getMessage());
        }
    }

    /**
     * Helper method for {@link testZf928ToAndBccHeadersShouldNotMix()}; extracts individual header lines
     *
     * @param Zend_Mail_Transport_Abstract $mock
     * @param string $type
     * @return string
     */
    protected function _getHeader(Zend_Mail_Transport_Abstract $mock, $type = 'To')
    {
        $headers = str_replace("\r\n", "\n", $mock->header);
        $headers = explode("\n", $mock->header);
        $return  = '';
        foreach ($headers as $header) {
            if (!empty($return)) {
                // Check for header continuation
                if (!preg_match('/^[a-z-]+:/i', $header)) {
                    $return .= "\r\n" . $header;
                    continue;
                } else {
                    break;
                }
            }
            if (preg_match('/^' . $type . ': /', $header)) {
                $return = $header;
            }
        }

        return $return;
    }

    public function testZf928ToAndBccHeadersShouldNotMix()
    {
        $mail = new Zend_Mail();
        $mail->setSubject('my subject');
        $mail->setBodyText('my body');
        $mail->setFrom('info@onlime.ch');
        $mail->addTo('to.address@email.com');
        $mail->addBcc('first.bcc@email.com');
        $mail->addBcc('second.bcc@email.com');

        // test with generic transport
        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);
        $to  = $this->_getHeader($mock);
        $bcc = $this->_getHeader($mock, 'Bcc');
        $this->assertContains('to.address@email.com', $to, $to);
        $this->assertNotContains('second.bcc@email.com', $to, $bcc);

        // test with sendmail-like transport
        $mock = new Zend_Mail_Transport_Sendmail_Mock();
        $mail->send($mock);
        $to  = $this->_getHeader($mock);
        $bcc = $this->_getHeader($mock, 'Bcc');
        // Remove the following line due to fixes by Simon
        // $this->assertNotContains('to.address@email.com', $to, $mock->header);
        $this->assertNotContains('second.bcc@email.com', $to, $bcc);
    }

    public function testZf927BlankLinesShouldPersist()
    {
        $mail = new Zend_Mail();
        $mail->setSubject('my subject');
        $mail->setBodyText("my body\r\n\r\n...after two newlines");
        $mail->setFrom('test@email.com');
        $mail->addTo('test@email.com');

        // test with generic transport
        $mock = new Zend_Mail_Transport_Sendmail_Mock();
        $mail->send($mock);
        $body = quoted_printable_decode($mock->body);
        $this->assertContains("\r\n\r\n...after", $body, $body);
    }

    public function testGetJustBodyText()
    {
        $text = "my body\r\n\r\n...after two newlines";
        $mail = new Zend_Mail();
        $mail->setBodyText($text);

        $this->assertContains('my body', $mail->getBodyText(true));
        $this->assertContains('after two newlines', $mail->getBodyText(true));
    }

    public function testGetJustBodyHtml()
    {
        $text = "<html><head></head><body><p>Some body text</p></body></html>";
        $mail = new Zend_Mail();
        $mail->setBodyHtml($text);

        $this->assertContains('Some body text', $mail->getBodyHtml(true));
    }

    public function testTypeAccessor()
    {
        $mail = new Zend_Mail();
        $this->assertNull($mail->getType());

        $mail->setType(Zend_Mime::MULTIPART_ALTERNATIVE);
        $this->assertEquals(Zend_Mime::MULTIPART_ALTERNATIVE, $mail->getType());

        $mail->setType(Zend_Mime::MULTIPART_RELATED);
        $this->assertEquals(Zend_Mime::MULTIPART_RELATED, $mail->getType());

        try {
            $mail->setType('text/plain');
            $this->fail('Invalid Zend_Mime type should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testDateSet()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('Date Test');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('Date Test');
        $mail->addTo('recipient@example.com');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertTrue(isset($mock->headers['Date']));
        $this->assertTrue(isset($mock->headers['Date'][0]));
        $this->assertTrue(strlen($mock->headers['Date'][0]) > 0);
    }

    public function testSetDateInt()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('Date Test');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('Date Test');
        $mail->addTo('recipient@example.com');
        $mail->setDate(362656800);

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertTrue(strpos(implode('', $mock->headers['Date']), 'Mon, 29 Jun 1981') === 0);
    }

    public function testSetDateString()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('Date Test');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('Date Test');
        $mail->addTo('recipient@example.com');
        $mail->setDate('1981-06-29T12:00:00');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertTrue(strpos(implode('', $mock->headers['Date']), 'Mon, 29 Jun 1981') === 0);
    }

    public function testSetDateObject()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('Date Test');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('Date Test');
        $mail->addTo('recipient@example.com');
        $mail->setDate(new Zend_Date('1981-06-29T12:00:00', Zend_Date::ISO_8601));

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertTrue(strpos(implode('', $mock->headers['Date']), 'Mon, 29 Jun 1981') === 0);
    }

    public function testSetDateInvalidString()
    {
        $mail = new Zend_Mail();

        try {
            $mail->setDate('invalid date');
            $this->fail('Invalid date should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testSetDateInvalidType()
    {
        $mail = new Zend_Mail();

        try {
            $mail->setDate(true);
            $this->fail('Invalid date should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testSetDateInvalidObject()
    {
        $mail = new Zend_Mail();

        try {
            $mail->setDate($mail);
            $this->fail('Invalid date should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testSetDateTwice()
    {
        $mail = new Zend_Mail();

        $mail->setDate();
        try {
            $mail->setDate(123456789);
            $this->fail('setting date twice should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testClearDate()
    {
        $mail = new Zend_Mail();

        $mail->setDate();
        $mail->clearDate();
        $this->assertFalse(isset($mock->headers['Date']));
    }

    public function testAutoMessageId()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('Message ID Test');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('Message ID Test');
        $mail->setMessageId();
        $mail->addTo('recipient@example.com');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertTrue(isset($mock->headers['Message-Id']));
        $this->assertTrue(isset($mock->headers['Message-Id'][0]));
        $this->assertTrue(strlen($mock->headers['Message-Id'][0]) > 0);
    }

    public function testSetMessageIdTwice()
    {
        $mail = new Zend_Mail();

        $mail->setMessageId();
        try {
            $mail->setMessageId();
            $this->fail('setting message-id twice should throw an exception');
        } catch (Exception $e) {
        }
    }

    public function testClearMessageId()
    {
        $mail = new Zend_Mail();

        $mail->setMessageId();
        $mail->clearMessageId();
        $this->assertFalse(isset($mock->headers['Message-Id']));
    }

    /**
     * @group ZF-6872
     */
    public function testSetReplyTo()
    {
        $mail = new Zend_Mail('UTF-8');
        $mail->setReplyTo("foo@zend.com", "\xe2\x82\xa0!");
        $headers = $mail->getHeaders();

        $this->assertEquals("=?UTF-8?Q?=E2=82=A0!?= <foo@zend.com>", $headers["Reply-To"][0]);
    }

    /**
     * @group ZF-1688
     * @group ZF-2559
     */
    public function testSetHeaderEncoding()
    {
        $mail = new Zend_Mail();
        $this->assertEquals(Zend_Mime::ENCODING_QUOTEDPRINTABLE, $mail->getHeaderEncoding());
        $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
        $this->assertEquals(Zend_Mime::ENCODING_BASE64,          $mail->getHeaderEncoding());
    }

    /**
     * @group ZF-1688
     * @dataProvider dataSubjects
     */
    public function testIfLongSubjectsHaveCorrectLineBreaksAndEncodingMarks($subject)
    {
        $mail = new Zend_Mail("UTF-8");
        $mail->setSubject($subject);
        $headers = $mail->getHeaders();
        $this->assertMailHeaderConformsToRfc($headers['Subject'][0]);
    }

    /**
     * @group ZF-7702
     */
    public function testReplyToIsNoRecipient() {
        $mail = new Zend_Mail();
        $mail->setReplyTo('foo@example.com','foobar');
        $this->assertEquals(0, count($mail->getRecipients()));
    }

    public function testGetReplyToReturnsReplyTo() {
        $mail = new Zend_Mail();
        $mail->setReplyTo('foo@example.com');
        $this->assertEquals('foo@example.com',$mail->getReplyTo());
    }

    /**
     * @expectedException Zend_Mail_Exception
     */
    public function testReplyToCantBeSetTwice() {
        $mail = new Zend_Mail();
        $mail->setReplyTo('user@example.com');
        $mail->setReplyTo('user2@example.com');
    }

    public function testDefaultFrom() {
        Zend_Mail::setDefaultFrom('john@example.com','John Doe');
        $this->assertEquals(array('email' => 'john@example.com','name' =>'John Doe'), Zend_Mail::getDefaultFrom());

        Zend_Mail::clearDefaultFrom();
        $this->assertEquals(null, Zend_Mail::getDefaultFrom());

        Zend_Mail::setDefaultFrom('john@example.com');
        $this->assertEquals(array('email' => 'john@example.com','name' => null), Zend_Mail::getDefaultFrom());
    }

    public function testDefaultReplyTo() {
        Zend_Mail::setDefaultReplyTo('john@example.com','John Doe');
        $this->assertEquals(array('email' => 'john@example.com','name' =>'John Doe'), Zend_Mail::getDefaultReplyTo());

        Zend_Mail::clearDefaultReplyTo();
        $this->assertEquals(null, Zend_Mail::getDefaultReplyTo());

        Zend_Mail::setDefaultReplyTo('john@example.com');
        $this->assertEquals(array('email' => 'john@example.com','name' => null), Zend_Mail::getDefaultReplyTo());
    }

    public function testSettingFromDefaults() {
        Zend_Mail::setDefaultFrom('john@example.com', 'John Doe');
        Zend_Mail::setDefaultReplyTo('foo@example.com','Foo Bar');

        $mail = new Zend_Mail();
        $headers = $mail->setFromToDefaultFrom() // test fluent interface
                        ->setReplyToFromDefault()
                        ->getHeaders();

        $this->assertEquals('john@example.com', $mail->getFrom());
        $this->assertEquals('foo@example.com', $mail->getReplyTo());
        $this->assertEquals('John Doe <john@example.com>', $headers['From'][0]);
        $this->assertEquals('Foo Bar <foo@example.com>', $headers['Reply-To'][0]);
    }

    public function testMethodSendUsesDefaults()
    {
        Zend_Mail::setDefaultFrom('john@example.com', 'John Doe');
        Zend_Mail::setDefaultReplyTo('foo@example.com','Foo Bar');

        $mail = new Zend_Mail();
        $mail->setBodyText('Defaults Test');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);
        $headers = $mock->headers;

        $this->assertTrue($mock->called);
        $this->assertEquals($mock->from, 'john@example.com');
        $this->assertEquals($headers['From'][0], 'John Doe <john@example.com>');
        $this->assertEquals($headers['Reply-To'][0], 'Foo Bar <foo@example.com>');
    }

    /**
     * @group ZF-9011
     */
    public function testSendmailTransportShouldAcceptConfigAndArrayAsConstructor()
    {
        $mail = new Zend_Mail("UTF-8");
        $mail->setBodyText('My Nice Test Text');
        $mail->addTo('foobar@example.com');
        $mail->setSubject('hello world!');

        $params = array('envelope'=> '-tjohn@example.com', 'foo' => '-fbar');
        $expected = '-tjohn@example.com -fbar';

        $transportMock = new Zend_Mail_Transport_Sendmail_Mock($params);
        $this->assertEquals($expected, $transportMock->parameters);

        $transportMock = new Zend_Mail_Transport_Sendmail_Mock(new Zend_Config($params));
        $this->assertEquals($expected, $transportMock->parameters);
    }

    /**
     * @group ZF-9011
     *
     */
    public function testSendmailTransportThrowsExceptionWithInvalidParams()
    {
        $mail = new Zend_Mail("UTF-8");
        $mail->setBodyText('My Nice Test Text');
        $mail->addTo('foobar@example.com');
        $mail->setSubject('hello world!');

        $transport = new Zend_Mail_Transport_Sendmail();
        $transport->parameters = true;
        try {
            $mail->send($transport);
            $this->fail('Exception should have been thrown, but wasn\'t');
        } catch(Zend_Mail_Transport_Exception $e) {
        	// do nothing
        }
    }

    public static function dataSubjects()
    {
        return array(
            array("Simple Ascii Subject"),
            array("Subject with US Specialchars: &%$/()"),
            array("Gimme more \xe2\x82\xa0!"),
            array("This is \xc3\xa4n germ\xc3\xa4n multiline s\xc3\xbcbject with rand\xc3\xb6m \xc3\xbcml\xc3\xa4uts."),
            array("Alle meine Entchen schwimmen in dem See, schwimmen in dem See, K\xc3\xb6pfchen in das Wasser, Schw\xc3\xa4nzchen in die H\xc3\xb6h!"),
            array("\xc3\xa4\xc3\xa4xxxxx\xc3\xa4\xc3\xa4\xc3\xa4\xc3\xa4\xc3\xa4\xc3\xa4\xc3\xa4"),
            array("\xd0\x90\xd0\x91\xd0\x92\xd0\x93\xd0\x94\xd0\x95 \xd0\x96\xd0\x97\xd0\x98\xd0\x99 \xd0\x9a\xd0\x9b\xd0\x9c\xd0\x9d"),
            array("Ich. Denke. Also. Bin. Ich! (Ein \xc3\xbcml\xc3\xa4\xc3\xbctautomat!)"),
        );
    }

    /**
     * Assertion that checks if a given mailing header string is RFC conform.
     *
     * @param  string $header
     * @return void
     */
    protected function assertMailHeaderConformsToRfc($header)
    {
        $this->numAssertions++;
        $parts = explode(Zend_Mime::LINEEND, $header);
        if(count($parts) > 0) {
            for($i = 0; $i < count($parts); $i++) {
                if(preg_match('/(=?[a-z0-9-_]+\?[q|b]{1}\?)/i', $parts[$i], $matches)) {
                    $dce = sprintf("=?%s", $matches[0]);
                    // Check that Delimiter, Charset, Encoding are at the front of the string
                    if(substr(trim($parts[$i]), 0, strlen($dce)) != $dce) {
                        $this->fail(sprintf(
                            "Header-Part '%s' in line '%d' has missing or malformated delimiter, charset, encoding information.",
                            $parts[$i],
                            $i+1
                        ));
                    }
                    // check that the encoded word is not too long.);
                    // this is only some kind of suggestion by the standard, in PHP its hard to hold it, so we do not enforce it here.
                    /*if(strlen($parts[$i]) > 75) {
                        $this->fail(sprintf(
                            "Each encoded-word is only allowed to be 75 chars long, but line %d is %s chars long: %s",
                            $i+1,
                            strlen($parts[$i]),
                            $parts[$i]
                        ));
                    }*/
                    // Check that the end-delmiter ?= is correctly placed
                    if(substr(trim($parts[$i]), -2, 2) != "?=") {
                        $this->fail(sprintf(
                            "Lines with an encoded-word have to end in ?=, but line %d does not: %s",
                            $i+1,
                            substr(trim($parts[$i]), -2, 2)
                        ));
                    }

                    // Check that only one encoded-word can be found per line.
                    if(substr_count($parts[$i], "=?") != 1) {
                        $this->fail(sprintf(
                            "Only one encoded-word is allowed per line in the header. It seems line %d contains more: %s",
                            $i+1,
                            $parts[$i]
                        ));
                    }

                    // Check that the encoded-text only contains US-ASCII chars, and no space
                    $encodedText = substr(trim($parts[$i]), strlen($dce), -2);
                    if(preg_match('/([\s]+)/', $encodedText)) {
                        $this->fail(sprintf(
                            "No whitespace characters allowed in encoded-text of line %d: %s",
                            $i+1,
                            $parts[$i]
                        ));
                    }
                    for($i = 0; $i < strlen($encodedText); $i++) {
                        if(ord($encodedText[$i]) > 127) {
                            $this->fail(sprintf(
                                "No non US-ASCII characters allowed, but line %d has them: %s",
                                 $i+1,
                                 $parts[$i]
                            ));
                        }
                    }
                } else if(Zend_Mime::isPrintable($parts[$i]) == false) {
                    $this->fail(sprintf(
                        "Encoded-word in line %d contains non printable characters.",
                        $i+1
                    ));
                }
            }
        }
    }

}
