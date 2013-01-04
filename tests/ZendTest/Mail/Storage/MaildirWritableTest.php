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

use Zend\Mail;
use Zend\Mail\Storage;
use Zend\Mail\Storage\Writable;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class MaildirWritableTest extends \PHPUnit_Framework_TestCase
{
    protected $_params;
    protected $_originalDir;
    protected $_tmpdir;
    protected $_subdirs = array('.', '.subfolder', '.subfolder.test');

    public function setUp()
    {
        $this->_originalDir = __DIR__ . '/../_files/test.maildir/';
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

        $this->_params = array();
        $this->_params['dirname'] = $this->_tmpdir;

        foreach ($this->_subdirs as $dir) {
            if ($dir != '.') {
                mkdir($this->_tmpdir . $dir);
            }
            foreach (array('cur', 'new') as $subdir) {
                if (!file_exists($this->_originalDir . $dir . '/' . $subdir)) {
                    continue;
                }
                mkdir($this->_tmpdir . $dir . '/' . $subdir);
                $dh = opendir($this->_originalDir . $dir . '/' . $subdir);
                while (($entry = readdir($dh)) !== false) {
                    $entry = $dir . '/' . $subdir . '/' . $entry;
                    if (!is_file($this->_originalDir . $entry)) {
                        continue;
                    }
                    copy($this->_originalDir . $entry, $this->_tmpdir . $entry);
                }
                closedir($dh);
            }
            copy($this->_originalDir . 'maildirsize', $this->_tmpdir . 'maildirsize');
        }
    }

    public function tearDown()
    {
        foreach (array_reverse($this->_subdirs) as $dir) {
            if (!file_exists($this->_tmpdir . $dir)) {
                continue;
            }
            foreach (array('cur', 'new', 'tmp') as $subdir) {
                if (!file_exists($this->_tmpdir . $dir . '/' . $subdir)) {
                    continue;
                }
                $dh = opendir($this->_tmpdir . $dir . '/' . $subdir);
                while (($entry = readdir($dh)) !== false) {
                    $entry = $this->_tmpdir . $dir . '/' . $subdir . '/' . $entry;
                    if (!is_file($entry)) {
                        continue;
                    }
                    unlink($entry);
                }
                closedir($dh);
                rmdir($this->_tmpdir . $dir . '/' . $subdir);
            }
            if ($dir != '.') {
                rmdir($this->_tmpdir . $dir);
            }
        }
        @unlink($this->_tmpdir . 'maildirsize');
    }

    public function testCreateFolder()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->createFolder('subfolder.test1');
        $mail->createFolder('test2', 'INBOX.subfolder');
        $mail->createFolder('test3', $mail->getFolders()->subfolder);
        $mail->createFolder('foo.bar');

        $mail->selectFolder($mail->getFolders()->subfolder->test1);
        $mail->selectFolder($mail->getFolders()->subfolder->test2);
        $mail->selectFolder($mail->getFolders()->subfolder->test3);
        $mail->selectFolder($mail->getFolders()->foo->bar);

        // to tear down
        $this->_subdirs[] = '.subfolder.test1';
        $this->_subdirs[] = '.subfolder.test2';
        $this->_subdirs[] = '.subfolder.test3';
        $this->_subdirs[] = '.foo';
        $this->_subdirs[] = '.foo.bar';
    }

    public function testCreateFolderEmptyPart()
    {
        $mail = new Writable\Maildir($this->_params);
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->createFolder('foo..bar');
    }

    public function testCreateFolderSlash()
    {
        $mail = new Writable\Maildir($this->_params);
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->createFolder('foo/bar');
    }

    public function testCreateFolderDirectorySeparator()
    {
        $mail = new Writable\Maildir($this->_params);
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->createFolder('foo' . DIRECTORY_SEPARATOR . 'bar');
    }

    public function testCreateFolderExistingDir()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        unset($mail->getFolders()->subfolder->test);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->createFolder('subfolder.test');
    }

    public function testCreateExistingFolder()
    {
        $mail = new Writable\Maildir($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->createFolder('subfolder.test');
    }

    public function testRemoveFolderName()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->removeFolder('INBOX.subfolder.test');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder($mail->getFolders()->subfolder->test);
    }

    public function testRemoveFolderInstance()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->removeFolder($mail->getFolders()->subfolder->test);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder($mail->getFolders()->subfolder->test);
    }

    public function testRemoveFolderWithChildren()
    {
        $mail = new Writable\Maildir($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->removeFolder($mail->getFolders()->subfolder);
    }

    public function testRemoveSelectedFolder()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->selectFolder('subfolder.test');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->removeFolder('subfolder.test');
    }

    public function testRemoveInvalidFolder()
    {
        $mail = new Writable\Maildir($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->removeFolder('thisFolderDoestNotExist');
    }

    public function testRenameFolder()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);

        $mail->renameFolder('INBOX.subfolder', 'INBOX.foo');
        $mail->renameFolder($mail->getFolders()->foo, 'subfolder');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->renameFolder('INBOX', 'foo');
    }

    public function testRenameSelectedFolder()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->selectFolder('subfolder.test');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->renameFolder('subfolder.test', 'foo');
    }

    public function testRenameToChild()
    {
        $mail = new Writable\Maildir($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->renameFolder('subfolder.test', 'subfolder.test.foo');
    }

    public function testAppend()
    {
        $mail = new Writable\Maildir($this->_params);
        $count = $mail->countMessages();

        $message = '';
        $message .= "From: me@example.org\r\n";
        $message .= "To: you@example.org\r\n";
        $message .= "Subject: append test\r\n";
        $message .= "\r\n";
        $message .= "This is a test\r\n";
        $mail->appendMessage($message);

        $this->assertEquals($count + 1, $mail->countMessages());
        $this->assertEquals($mail->getMessage($count + 1)->subject, 'append test');
    }

    public function testCopy()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);

        $mail->selectFolder('subfolder.test');
        $count = $mail->countMessages();
        $mail->selectFolder('INBOX');
        $message = $mail->getMessage(1);

        $mail->copyMessage(1, 'subfolder.test');
        $mail->selectFolder('subfolder.test');
        $this->assertEquals($count + 1, $mail->countMessages());
        $this->assertEquals($mail->getMessage($count + 1)->subject, $message->subject);
        $this->assertEquals($mail->getMessage($count + 1)->from, $message->from);
        $this->assertEquals($mail->getMessage($count + 1)->to, $message->to);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->copyMessage(1, 'justARandomFolder');
    }

    public function testSetFlags()
    {
        $mail = new Writable\Maildir($this->_params);

        $mail->setFlags(1, array(Storage::FLAG_SEEN));
        $message = $mail->getMessage(1);
        $this->assertTrue($message->hasFlag(Storage::FLAG_SEEN));
        $this->assertFalse($message->hasFlag(Storage::FLAG_FLAGGED));

        $mail->setFlags(1, array(Storage::FLAG_SEEN, Storage::FLAG_FLAGGED));
        $message = $mail->getMessage(1);
        $this->assertTrue($message->hasFlag(Storage::FLAG_SEEN));
        $this->assertTrue($message->hasFlag(Storage::FLAG_FLAGGED));

        $mail->setFlags(1, array(Storage::FLAG_FLAGGED));
        $message = $mail->getMessage(1);
        $this->assertFalse($message->hasFlag(Storage::FLAG_SEEN));
        $this->assertTrue($message->hasFlag(Storage::FLAG_FLAGGED));

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->setFlags(1, array(Storage::FLAG_RECENT));
    }

    public function testSetFlagsRemovedFile()
    {
        $mail = new Writable\Maildir($this->_params);
        unlink($this->_params['dirname'] . 'cur/1000000000.P1.example.org:2,S');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
    }

    public function testRemove()
    {
        $mail = new Writable\Maildir($this->_params);
        $count = $mail->countMessages();

        $mail->removeMessage(1);
        $this->assertEquals($mail->countMessages(), --$count);

        unset($mail[2]);
        $this->assertEquals($mail->countMessages(), --$count);
    }

    public function testRemoveRemovedFile()
    {
        $mail = new Writable\Maildir($this->_params);
        unlink($this->_params['dirname'] . 'cur/1000000000.P1.example.org:2,S');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->removeMessage(1);
    }

    public function testCheckQuota()
    {
        $mail = new Writable\Maildir($this->_params);
        $this->assertFalse($mail->checkQuota());
    }

    public function testCheckQuotaDetailed()
    {
        $mail = new Writable\Maildir($this->_params);
        $quotaResult = array(
            'size'  => 2129,
            'count' => 5,
            'quota' => array(
                    'count' => 10,
                    'L'     => 1,
                    'size'  => 3000
                ),
            'over_quota' => false
        );
        $this->assertEquals($quotaResult, $mail->checkQuota(true));
    }

    public function testSetQuota()
    {
        $mail = new Writable\Maildir($this->_params);
        $this->assertNull($mail->getQuota());

        $mail->setQuota(true);
        $this->assertTrue($mail->getQuota());

        $mail->setQuota(false);
        $this->assertFalse($mail->getQuota());

        $mail->setQuota(array('size' => 100, 'count' => 2, 'X' => 0));
        $this->assertEquals($mail->getQuota(), array('size' => 100, 'count' => 2, 'X' => 0));
        $this->assertEquals($mail->getQuota(true), array('size' => 3000, 'L' => 1, 'count' => 10));

        $quotaResult = array(
            'size'  => 2129,
            'count' => 5,
            'quota' => array(
                    'size'  => 100,
                    'count' => 2,
                    'X'     => 0
                ),
            'over_quota' => true
        );
        $this->assertEquals($quotaResult, $mail->checkQuota(true, true));
        $this->assertEquals(array('size' => 100, 'count' => 2, 'X' => 0), $mail->getQuota(true));
    }

    public function testMissingMaildirsize()
    {
        $mail = new Writable\Maildir($this->_params);
        $this->assertEquals($mail->getQuota(true), array('size' => 3000, 'L' => 1, 'count' => 10));

        unlink($this->_tmpdir . 'maildirsize');

        $this->assertNull($mail->getQuota());

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getQuota(true);
    }

    public function testMissingMaildirsizeWithFixedQuota()
    {
        $mail = new Writable\Maildir($this->_params);
        unlink($this->_tmpdir . 'maildirsize');
        $mail->setQuota(array('size' => 100, 'count' => 2, 'X' => 0));

        $quotaResult = array(
            'size'  => 2129,
            'count' => 5,
            'quota' => array(
                    'size'  => 100,
                    'count' => 2,
                    'X'     => 0
                ),
            'over_quota' => true
        );
        $this->assertEquals($mail->checkQuota(true), $quotaResult);

        $this->assertEquals($mail->getQuota(true), $quotaResult['quota']);
    }

    public function testAppendMessage()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->setQuota(array('size' => 3000, 'count' => 6, 'X' => 0));
        $this->assertFalse($mail->checkQuota(false, true));
        $mail->appendMessage("Subject: test\r\n\r\n");
        $quotaResult = array(
            'size'  => 2613,
            'count' => 7,
            'quota' => array(
                    'size'  => 3000,
                    'count' => 6,
                    'X'     => 0
                ),
            'over_quota' => true
        );
        $this->assertEquals($mail->checkQuota(true), $quotaResult);

        $mail->setQuota(false);
        $this->assertTrue($mail->checkQuota());

        $mail->appendMessage("Subject: test\r\n\r\n");

        $mail->setQuota(true);
        $this->assertTrue($mail->checkQuota());

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->appendMessage("Subject: test\r\n\r\n");
    }

    public function testRemoveMessage()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->setQuota(array('size' => 3000, 'count' => 5, 'X' => 0));
        $this->assertTrue($mail->checkQuota(false, true));

        $mail->removeMessage(1);
        $this->assertFalse($mail->checkQuota());
    }

    public function testCopyMessage()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $mail->setQuota(array('size' => 3000, 'count' => 6, 'X' => 0));
        $this->assertFalse($mail->checkQuota(false, true));
        $mail->copyMessage(1, 'subfolder');
        $quotaResult = array(
            'size'  => 2993,
            'count' => 7,
            'quota' => array(
                    'size'  => 3000,
                    'count' => 6,
                    'X'     => 0
                ),
            'over_quota' => true
        );
        $this->assertEquals($mail->checkQuota(true), $quotaResult);
    }

    public function testAppendStream()
    {
        $mail = new Writable\Maildir($this->_params);
        $fh = fopen('php://memory', 'rw');
        fputs($fh, "Subject: test\r\n\r\n");
        fseek($fh, 0);
        $mail->appendMessage($fh);
        fclose($fh);

        $this->assertEquals($mail->getMessage($mail->countMessages())->subject, 'test');
    }

    public function testMove()
    {
        $this->markTestIncomplete("Fail");
        $mail = new Writable\Maildir($this->_params);
        $target = $mail->getFolders()->subfolder->test;
        $mail->selectFolder($target);
        $toCount = $mail->countMessages();
        $mail->selectFolder('INBOX');
        $fromCount = $mail->countMessages();
        $mail->moveMessage(1, $target);


        $this->assertEquals($fromCount - 1, $mail->countMessages());
        $mail->selectFolder($target);
        $this->assertEquals($toCount + 1, $mail->countMessages());
    }

    public function testInitExisting()
    {
        // this should be a noop
        Writable\Maildir::initMaildir($this->_params['dirname']);
        $mail = new Writable\Maildir($this->_params);
        $this->assertEquals($mail->countMessages(), 5);
    }

    public function testInit()
    {
        $this->tearDown();

        // should fail now
        $e = null;
        try {
            $mail = new Writable\Maildir($this->_params);
            $this->fail('empty maildir should not be accepted');
        } catch (\Exception $e) {
        }

        Writable\Maildir::initMaildir($this->_params['dirname']);
        $mail = new Writable\Maildir($this->_params);
        $this->assertEquals($mail->countMessages(), 0);
    }

    public function testCreate()
    {
        $this->tearDown();

        // should fail now
        $e = null;
        try {
            $mail = new Writable\Maildir($this->_params);
            $this->fail('empty maildir should not be accepted');
        } catch (\Exception $e) {
        }

        $this->_params['create'] = true;
        $mail = new Writable\Maildir($this->_params);
        $this->assertEquals($mail->countMessages(), 0);
    }
}
