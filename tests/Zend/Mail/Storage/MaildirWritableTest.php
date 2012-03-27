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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mail\Storage;

use Zend\Mail,
    Zend\Mail\Storage,
    Zend\Mail\Storage\Writable;


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

        if (!is_dir($this->_originalDir . '/cur/')) {
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
        $mail = new Writable\Maildir($this->_params);
        $mail->createFolder('subfolder.test1');
        $mail->createFolder('test2', 'INBOX.subfolder');
        $mail->createFolder('test3', $mail->getFolders()->subfolder);
        $mail->createFolder('foo.bar');

        try {
            $mail->selectFolder($mail->getFolders()->subfolder->test1);
            $mail->selectFolder($mail->getFolders()->subfolder->test2);
            $mail->selectFolder($mail->getFolders()->subfolder->test3);
            $mail->selectFolder($mail->getFolders()->foo->bar);
        } catch (\Exception $e) {
            $this->fail('could not get new folders');
        }

        // to tear down
        $this->_subdirs[] = '.subfolder.test1';
        $this->_subdirs[] = '.subfolder.test2';
        $this->_subdirs[] = '.subfolder.test3';
        $this->_subdirs[] = '.foo';
        $this->_subdirs[] = '.foo.bar';
    }

    public function testCreateFolderEmtpyPart()
    {
        $mail = new Writable\Maildir($this->_params);
        try {
            $mail->createFolder('foo..bar');
        } catch (\Exception $e) {
            return; //ok
        }

        $this->fail('no exception while creating folder with empty part name');
    }

    public function testCreateFolderSlash()
    {
        $mail = new Writable\Maildir($this->_params);
        try {
            $mail->createFolder('foo/bar');
        } catch (\Exception $e) {
            return; //ok
        }

        $this->fail('no exception while creating folder with slash');
    }

    public function testCreateFolderDirectorySeparator()
    {
        $mail = new Writable\Maildir($this->_params);
        try {
            $mail->createFolder('foo' . DIRECTORY_SEPARATOR . 'bar');
        } catch (\Exception $e) {
            return; //ok
        }

        $this->fail('no exception while creating folder with DIRECTORY_SEPARATOR');
    }

    public function testCreateFolderExistingDir()
    {
        $mail = new Writable\Maildir($this->_params);
        unset($mail->getFolders()->subfolder->test);

        try {
            $mail->createFolder('subfolder.test');
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to create existing folder');
    }

    public function testCreateExistingFolder()
    {
        $mail = new Writable\Maildir($this->_params);

        try {
            $mail->createFolder('subfolder.test');
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to create existing folder');
    }

    public function testRemoveFolderName()
    {
        $mail = new Writable\Maildir($this->_params);
        $mail->removeFolder('INBOX.subfolder.test');

        try {
            $mail->selectFolder($mail->getFolders()->subfolder->test);
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('folder still exists');
    }

    public function testRemoveFolderInstance()
    {
        $mail = new Writable\Maildir($this->_params);
        $mail->removeFolder($mail->getFolders()->subfolder->test);

        try {
            $mail->selectFolder($mail->getFolders()->subfolder->test);
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('folder still exists');
    }

    public function testRemoveFolderWithChildren()
    {
        $mail = new Writable\Maildir($this->_params);

        try {
            $mail->removeFolder($mail->getFolders()->subfolder);
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to remove a folder with children');
    }

    public function testRemoveSelectedFolder()
    {
        $mail = new Writable\Maildir($this->_params);
        $mail->selectFolder('subfolder.test');

        try {
            $mail->removeFolder('subfolder.test');
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('no error while removing selected folder');
    }

    public function testRemoveInvalidFolder()
    {
        $mail = new Writable\Maildir($this->_params);

        try {
            $mail->removeFolder('thisFolderDoestNotExist');
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('no error while removing invalid folder');
    }

    public function testRenameFolder()
    {
        $mail = new Writable\Maildir($this->_params);
        try {
            $mail->renameFolder('INBOX.subfolder', 'INBOX.foo');
            $mail->renameFolder($mail->getFolders()->foo, 'subfolder');
        } catch (\Exception $e) {
            $this->fail('renaming failed');
        }

        try {
            $mail->renameFolder('INBOX', 'foo');
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('no error while renaming INBOX');
    }

    public function testRenameSelectedFolder()
    {
        $mail = new Writable\Maildir($this->_params);
        $mail->selectFolder('subfolder.test');

        try {
            $mail->renameFolder('subfolder.test', 'foo');
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('no error while renaming selected folder');
    }

    public function testRenameToChild()
    {
        $mail = new Writable\Maildir($this->_params);

        try {
            $mail->renameFolder('subfolder.test', 'subfolder.test.foo');
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('no error while renaming folder to child of old');
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

        try {
            $mail->copyMessage(1, 'justARandomFolder');
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('no error while copying to wrong folder');
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

        try {
            $mail->setFlags(1, array(Storage::FLAG_RECENT));
        } catch (\Exception $e) {
            return; // ok
        }
        $this->fail('should not be able to set recent flag');
    }

    public function testSetFlagsRemovedFile()
    {
        $mail = new Writable\Maildir($this->_params);
        unlink($this->_params['dirname'] . 'cur/1000000000.P1.example.org:2,S');

        try {
            $mail->setFlags(1, array(Storage::FLAG_FLAGGED));
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to set flags with removed file');
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

        try {
            $mail->removeMessage(1);
        } catch (\Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to remove message which is already removed in fs');
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
            'size'  => 2596,
            'count' => 6,
            'quota' => array(
                    'count' => 10,
                    'L'     => 1,
                    'size'  => 3000
                ),
            'over_quota' => false
        );
        $this->assertEquals($mail->checkQuota(true), $quotaResult);
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
            'size'  => 2596,
            'count' => 6,
            'quota' => array(
                    'size'  => 100,
                    'count' => 2,
                    'X'     => 0
                ),
            'over_quota' => true
        );
        $this->assertEquals($mail->checkQuota(true, true), $quotaResult);

        $this->assertEquals($mail->getQuota(true), array('size' => 100, 'count' => 2, 'X' => 0));
    }

    public function testMissingMaildirsize()
    {
        $mail = new Writable\Maildir($this->_params);
        $this->assertEquals($mail->getQuota(true), array('size' => 3000, 'L' => 1, 'count' => 10));

        unlink($this->_tmpdir . 'maildirsize');

        $this->assertNull($mail->getQuota());

        try {
            $mail->getQuota(true);
        } catch(Mail\Exception $e) {
            // ok
            return;
        }
        $this->fail('get quota from file should fail if file is missing');
    }

    public function testMissingMaildirsizeWithFixedQuota()
    {
        $mail = new Writable\Maildir($this->_params);
        unlink($this->_tmpdir . 'maildirsize');
        $mail->setQuota(array('size' => 100, 'count' => 2, 'X' => 0));

        $quotaResult = array(
            'size'  => 2596,
            'count' => 6,
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
        try {
            $mail->appendMessage("Subject: test\r\n\r\n");
        } catch(Mail\Exception $e) {
            $this->fail('appending should not fail if quota check is not active');
        }

        $mail->setQuota(true);
        $this->assertTrue($mail->checkQuota());
        try {
            $mail->appendMessage("Subject: test\r\n\r\n");
        } catch(Mail\Exception $e) {
            // ok
            return;
        }
        $this->fail('appending after being over quota should fail');
    }

    public function testRemoveMessage()
    {
        $mail = new Writable\Maildir($this->_params);
        $mail->setQuota(array('size' => 3000, 'count' => 5, 'X' => 0));
        $this->assertTrue($mail->checkQuota(false, true));

        $mail->removeMessage(1);
        $this->assertFalse($mail->checkQuota());
    }

    public function testCopyMessage()
    {
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
        } catch (\Exception $e) {
        }

        if ($e === null) {
            $this->fail('empty maildir should not be accepted');
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
        } catch (\Exception $e) {
        }

        if ($e === null) {
            $this->fail('empty maildir should not be accepted');
        }

        $this->_params['create'] = true;
        $mail = new Writable\Maildir($this->_params);
        $this->assertEquals($mail->countMessages(), 0);
    }
}
