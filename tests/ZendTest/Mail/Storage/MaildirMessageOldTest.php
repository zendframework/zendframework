<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace ZendTest\Mail\Storage;

use Zend\Mail\Storage;

/**
 * Maildir class, which uses old message class
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class MaildirOldMessage extends Storage\Maildir
{
    /**
     * used message class
     * @var string
     */
    protected $_messageClass = 'Zend\Mail\Storage\Message';
}

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class MaildirMessageOldTest extends \PHPUnit_Framework_TestCase
{
    protected $_originalMaildir;
    protected $_maildir;
    protected $_tmpdir;

    public function setUp()
    {
        $this->_originalMaildir = __DIR__ . '/../_files/test.maildir/';
        if (!constant('TESTS_ZEND_MAIL_MAILDIR_ENABLED')) {
            $this->markTestSkipped('You have to unpack maildir.tar in Zend/Mail/_files/test.maildir/ '
                                 . 'directory before enabling the maildir tests');
            return;
        }

        if ($this->_tmpdir == null) {
            if (TESTS_ZEND_MAIL_TEMPDIR != null) {
                $this->_tmpdir = TESTS_ZEND_MAIL_TEMPDIR;
            } else {
                $this->_tmpdir = __DIR__ . '/../_files/test.tmp/';
            }
            if (!file_exists($this->_tmpdir)) {
                mkdir($this->_tmpdir);
            }
            $count = 0;
            $dh = opendir($this->_tmpdir);
            while (readdir($dh) !== false) {
                ++$count;
            }
            closedir($dh);
            if ($count != 2) {
                $this->markTestSkipped('Are you sure your tmp dir is a valid empty dir?');
                return;
            }
        }

        $this->_maildir = $this->_tmpdir;

        foreach (array('cur', 'new') as $dir) {
            mkdir($this->_tmpdir . $dir);
            $dh = opendir($this->_originalMaildir . $dir);
            while (($entry = readdir($dh)) !== false) {
                $entry = $dir . '/' . $entry;
                if (!is_file($this->_originalMaildir . $entry)) {
                    continue;
                }
                copy($this->_originalMaildir . $entry, $this->_tmpdir . $entry);
            }
            closedir($dh);
        }
    }

    public function tearDown()
    {
        foreach (array('cur', 'new') as $dir) {
            if (!is_dir($this->_tmpdir . $dir)) {
                continue;
            }
            $dh = opendir($this->_tmpdir . $dir);
            while (($entry = readdir($dh)) !== false) {
                $entry = $this->_tmpdir . $dir . '/' . $entry;
                if (!is_file($entry)) {
                    continue;
                }
                unlink($entry);
            }
            closedir($dh);
            rmdir($this->_tmpdir . $dir);
        }
    }


    public function testFetchHeader()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    public function testFetchTopBody()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/
    public function testFetchMessageHeader()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testHasFlag()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));

        $this->assertFalse($mail->getMessage(5)->hasFlag(Storage::FLAG_SEEN));
        $this->assertTrue($mail->getMessage(5)->hasFlag(Storage::FLAG_RECENT));
        $this->assertTrue($mail->getMessage(2)->hasFlag(Storage::FLAG_FLAGGED));
        $this->assertFalse($mail->getMessage(2)->hasFlag(Storage::FLAG_ANSWERED));
    }

    public function testGetFlags()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));

        $flags = $mail->getMessage(1)->getFlags();
        $this->assertTrue(isset($flags[Storage::FLAG_SEEN]));
        $this->assertTrue(in_array(Storage::FLAG_SEEN, $flags));
    }

    public function testFetchPart()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));
        $this->assertEquals($mail->getMessage(4)->getPart(2)->contentType, 'text/x-vertical');
    }

    public function testPartSize()
    {
        $mail = new MaildirOldMessage(array('dirname' => $this->_maildir));
        $this->assertEquals($mail->getMessage(4)->getPart(2)->getSize(), 80);
    }
}
