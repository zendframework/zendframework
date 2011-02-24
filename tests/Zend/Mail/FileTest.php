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
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Mail;
use Zend\Mail;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    protected $_params;
    protected $_transport;
    protected $_tmpdir;

    public function setUp()
    {
        if (TESTS_ZEND_MAIL_TEMPDIR != null) {
            $this->_tmpdir = TESTS_ZEND_MAIL_TEMPDIR;
        } else {
            $this->_tmpdir = dirname(__FILE__) . '/_files/test.file/';
        }
        
        if (!file_exists($this->_tmpdir)) {
            mkdir($this->_tmpdir);
        }

        $this->_cleanDir($this->_tmpdir);
    }
    
    public function tearDown()
    {
        $this->_cleanDir($this->_tmpdir);
    }

    protected function _cleanDir($dir)
    {
        $entries = scandir($dir);
        foreach ($entries as $entry) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $fullname = $dir . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($fullname)) {
                $this->_cleanDir($fullname);
                rmdir($fullname);
            } else {
                unlink($fullname);
            }
        }
    }

    public function testTransportSetup()
    {
        $transport = new Mail\Transport\File();

        $callback = function() {
            return 'test';
        };

        $transport = new Mail\Transport\File(array(
            'path'     => $this->_tmpdir,
            'callback' => $callback,
        ));

        $this->assertEquals($this->_tmpdir, $transport->getPath());
        $this->assertSame($callback, $transport->getCallback());
    }

    protected function _prepareMail()
    {
        $mail = new Mail\Mail();
        $mail->setBodyText('This is the text of the mail.');
        $mail->setFrom('alexander@example.com', 'Alexander Steshenko');
        $mail->addTo('oleg@example.com', 'Oleg Lobach');
        $mail->setSubject('TestSubject');

        return $mail;
    }

    public function testNotWritablePathFailure()
    {
        $transport = new Mail\Transport\File(array(
            'path' => $this->_tmpdir . '/not_existing/directory'
        ));

        $mail = $this->_prepareMail();

        $this->setExpectedException('Zend\Mail\Transport\Exception\RuntimeException', 'not writable');
        $mail->send($transport);
    }

    public function testTransportSendMail()
    {
        $transport = new Mail\Transport\File(array('path' => $this->_tmpdir));

        $mail = $this->_prepareMail();
        $mail->send($transport);

        $entries = scandir($this->_tmpdir);
        $this->assertTrue(count($entries) == 3);
        foreach ($entries as $entry) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $filename = $this->_tmpdir . DIRECTORY_SEPARATOR . $entry;
        }

        $email = file_get_contents($filename);
        $this->assertContains('To: Oleg Lobach <oleg@example.com>', $email);
        $this->assertContains('Subject: TestSubject', $email);
        $this->assertContains('From: Alexander Steshenko <alexander@example.com>', $email);
        $this->assertContains("This is the text of the mail.", $email);
    }

    public function testPrependToClosure()
    {
        // callback utilizes default callback and prepends recipient email
        $callback = function($transport) {
            $defaultCallback = $transport->getDefaultCallback();
            return $transport->recipients . '_' . $defaultCallback($transport);
        };

        $transport = new Mail\Transport\File(array(
            'path' => $this->_tmpdir,
            'callback' => $callback
        ));

        $mail = $this->_prepareMail();
        $mail->send($transport);

        $entries = scandir($this->_tmpdir);
        $this->assertTrue(count($entries) == 3);
        foreach ($entries as $entry) {
            if ($entry == '.' || $entry == '..') {
                continue;
            } else {
                break;
            }
        }

        // file name should now contain recipient email address
        $this->assertContains('oleg@example.com', $entry);
        // and default callback part
        $this->assertContains('ZendMail', $entry);
    }
}
