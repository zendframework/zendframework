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

use Zend\Config;
use Zend\Mail\Protocol;
use Zend\Mail\Storage;
use Zend\Mail\Storage\Exception;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class ImapTest extends \PHPUnit_Framework_TestCase
{
    protected $_params;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_MAIL_IMAP_ENABLED')) {
            $this->markTestSkipped('Zend_Mail IMAP tests are not enabled');
        }
        $this->_params = array('host'     => TESTS_ZEND_MAIL_IMAP_HOST,
                               'user'     => TESTS_ZEND_MAIL_IMAP_USER,
                               'password' => TESTS_ZEND_MAIL_IMAP_PASSWORD);
        if (defined('TESTS_ZEND_MAIL_SERVER_TESTDIR') && TESTS_ZEND_MAIL_SERVER_TESTDIR) {
            if (!file_exists(TESTS_ZEND_MAIL_SERVER_TESTDIR . DIRECTORY_SEPARATOR . 'inbox')
             && !file_exists(TESTS_ZEND_MAIL_SERVER_TESTDIR . DIRECTORY_SEPARATOR . 'INBOX')) {
                $this->markTestSkipped('There is no file name "inbox" or "INBOX" in '
                                       . TESTS_ZEND_MAIL_SERVER_TESTDIR . '. I won\'t use it for testing. '
                                       . 'This is you safety net. If you think it is the right directory just '
                                       . 'create an empty file named INBOX or remove/deactived this message.');
            }

            $this->_cleanDir(TESTS_ZEND_MAIL_SERVER_TESTDIR);
            $this->_copyDir(__DIR__ . '/../_files/test.' . TESTS_ZEND_MAIL_SERVER_FORMAT,
                            TESTS_ZEND_MAIL_SERVER_TESTDIR);
        }
    }

    protected function _cleanDir($dir)
    {
        $dh = opendir($dir);
        while (($entry = readdir($dh)) !== false) {
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
        closedir($dh);
    }

    protected function _copyDir($dir, $dest)
    {
        $dh = opendir($dir);
        while (($entry = readdir($dh)) !== false) {
            if ($entry == '.' || $entry == '..' || $entry == '.svn') {
                continue;
            }
            $fullname = $dir  . DIRECTORY_SEPARATOR . $entry;
            $destname = $dest . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($fullname)) {
                mkdir($destname);
                $this->_copyDir($fullname, $destname);
            } else {
                copy($fullname, $destname);
            }
        }
        closedir($dh);
    }

    public function testConnectOk()
    {
        new Storage\Imap($this->_params);
    }

    public function testConnectConfig()
    {
        new Storage\Imap(new Config\Config($this->_params));
    }

    public function testConnectFailure()
    {
        $this->_params['host'] = 'example.example';
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($this->_params);
    }

    public function testNoParams()
    {
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap(array());
    }


    public function testConnectSSL()
    {
        if (!TESTS_ZEND_MAIL_IMAP_SSL) {
            return;
        }

        $this->_params['ssl'] = 'SSL';
        new Storage\Imap($this->_params);

    }

    public function testConnectTLS()
    {
        if (!TESTS_ZEND_MAIL_IMAP_TLS) {
            return;
        }

        $this->_params['ssl'] = 'TLS';
        new Storage\Imap($this->_params);
    }

    public function testInvalidService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_IMAP_INVALID_PORT;
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($this->_params);
    }

    public function testWrongService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_IMAP_WRONG_PORT;
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($this->_params);
    }

    public function testWrongUsername()
    {
        // this also triggers ...{chars}<NL>token for coverage
        $this->_params['user'] = "there is no\nnobody";
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($this->_params);
    }

    public function testWithInstanceConstruction()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $protocol->login($this->_params['user'], $this->_params['password']);
        // if $protocol is invalid the constructor fails while selecting INBOX
        new Storage\Imap($protocol);
    }

    public function testWithNotConnectedInstance()
    {
        $protocol = new Protocol\Imap();
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($protocol);
    }

    public function testWithNotLoggedInstance()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($protocol);
    }

    public function testWrongFolder()
    {
        $this->_params['folder'] = 'this folder does not exist on your server';

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($this->_params);
    }


    public function testClose()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->close();
    }
/*
    currently imap has no top

    public function testHasTop()
    {
        $mail = new Storage\Imap($this->_params);

        $this->assertTrue($mail->hasTop);
    }
*/
    public function testHasCreate()
    {
        $mail = new Storage\Imap($this->_params);

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->noop();
    }

    public function testCount()
    {
        $mail = new Storage\Imap($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(7, $count);
    }

    public function testSize()
    {
        $mail = new Storage\Imap($this->_params);
        $shouldSizes = array(1 => 397, 89, 694, 452, 497, 101, 139);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Storage\Imap($this->_params);

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Storage\Imap($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    currently imap has no top

    public function testFetchTopBody()
    {
        $mail = new Storage\Imap($this->_params);

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/
    public function testFetchMessageHeader()
    {
        $mail = new Storage\Imap($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Storage\Imap($this->_params);

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testRemove()
    {
        $mail = new Storage\Imap($this->_params);

        $count = $mail->countMessages();
        $mail->removeMessage(1);
        $this->assertEquals($mail->countMessages(), $count - 1);
    }

    public function testTooLateCount()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->close();
        // after closing we can't count messages

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->countMessages();
    }

    public function testLoadUnkownFolder()
    {
        $this->_params['folder'] = 'UnknownFolder';
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Imap($this->_params);
    }

    public function testChangeFolder()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->selectFolder('subfolder/test');

        $this->assertEquals($mail->getCurrentFolder(), 'subfolder/test');
    }

    public function testUnknownFolder()
    {
        $mail = new Storage\Imap($this->_params);
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->selectFolder('/Unknown/Folder/');
    }

    public function testGlobalName()
    {
        $mail = new Storage\Imap($this->_params);
        $this->assertEquals($mail->getFolders()->subfolder->__toString(), 'subfolder');
    }

    public function testLocalName()
    {
        $mail = new Storage\Imap($this->_params);
        $this->assertEquals($mail->getFolders()->subfolder->key(), 'test');
    }

    public function testKeyLocalName()
    {
        $mail = new Storage\Imap($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder'      => 'subfolder',
                                'subfolder/test' => 'test',
                                'INBOX'          => 'INBOX');
        $found_folders = array();

        foreach ($iterator as $localName => $folder) {
            if (!isset($search_folders[$folder->getGlobalName()])) {
                continue;
            }

            // explicit call of __toString() needed for PHP < 5.2
            $found_folders[$folder->__toString()] = $localName;
        }

        $this->assertEquals($search_folders, $found_folders);
    }

    public function testSelectable()
    {
        $mail = new Storage\Imap($this->_params);
        $iterator = new \RecursiveIteratorIterator($mail->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $localName => $folder) {
            $this->assertEquals($localName, $folder->getLocalName());
        }
    }


    public function testCountFolder()
    {
        $mail = new Storage\Imap($this->_params);

        $mail->selectFolder('subfolder/test');
        $count = $mail->countMessages();
        $this->assertEquals(1, $count);
    }

    public function testSizeFolder()
    {
        $mail = new Storage\Imap($this->_params);

        $mail->selectFolder('subfolder/test');
        $sizes = $mail->getSize();
        $this->assertEquals(array(1 => 410), $sizes);
    }

    public function testFetchHeaderFolder()
    {
        $mail = new Storage\Imap($this->_params);

        $mail->selectFolder('subfolder/test');
        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Message in subfolder', $subject);
    }

    public function testHasFlag()
    {
        $mail = new Storage\Imap($this->_params);

        $this->assertTrue($mail->getMessage(1)->hasFlag(Storage::FLAG_RECENT));
    }

    public function testGetFlags()
    {
        $mail = new Storage\Imap($this->_params);

        $flags = $mail->getMessage(1)->getFlags();
        $this->assertTrue(isset($flags[Storage::FLAG_RECENT]));
        $this->assertTrue(in_array(Storage::FLAG_RECENT, $flags));
    }

    public function testRawHeader()
    {
        $mail = new Storage\Imap($this->_params);

        $this->assertTrue(strpos($mail->getRawHeader(1), "\r\nSubject: Simple Message\r\n") > 0);
    }

    public function testUniqueId()
    {
        $mail = new Storage\Imap($this->_params);

        $this->assertTrue($mail->hasUniqueId);
        $this->assertEquals(1, $mail->getNumberByUniqueId($mail->getUniqueId(1)));

        $ids = $mail->getUniqueId();
        foreach ($ids as $num => $id) {
            foreach ($ids as $inner_num => $inner_id) {
                if ($num == $inner_num) {
                    continue;
                }
                if ($id == $inner_id) {
                    $this->fail('not all ids are unique');
                }
            }

            if ($mail->getNumberByUniqueId($id) != $num) {
                    $this->fail('reverse lookup failed');
            }
        }
    }

    public function testWrongUniqueId()
    {
        $mail = new Storage\Imap($this->_params);
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getNumberByUniqueId('this_is_an_invalid_id');
    }

    public function testCreateFolder()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->createFolder('subfolder/test1');
        $mail->createFolder('test2', 'subfolder');
        $mail->createFolder('test3', $mail->getFolders()->subfolder);

        $mail->getFolders()->subfolder->test1;
        $mail->getFolders()->subfolder->test2;
        $mail->getFolders()->subfolder->test3;
    }

    public function testCreateExistingFolder()
    {
        $mail = new Storage\Imap($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->createFolder('subfolder/test');
    }

    public function testRemoveFolderName()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->removeFolder('subfolder/test');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getFolders()->subfolder->test;
    }

    public function testRemoveFolderInstance()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->removeFolder($mail->getFolders()->subfolder->test);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getFolders()->subfolder->test;
    }

    public function testRemoveInvalidFolder()
    {
        $mail = new Storage\Imap($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->removeFolder('thisFolderDoestNotExist');
    }

    public function testRenameFolder()
    {
        $mail = new Storage\Imap($this->_params);

        $mail->renameFolder('subfolder/test', 'subfolder/test1');
        $mail->renameFolder($mail->getFolders()->subfolder->test1, 'subfolder/test');

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->renameFolder('subfolder/test', 'INBOX');
    }

    public function testAppend()
    {
        $mail = new Storage\Imap($this->_params);
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

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->appendMessage('');
    }

    public function testCopy()
    {
        $mail = new Storage\Imap($this->_params);

        $mail->selectFolder('subfolder/test');
        $count = $mail->countMessages();
        $mail->selectFolder('INBOX');
        $message = $mail->getMessage(1);

        $mail->copyMessage(1, 'subfolder/test');
        $mail->selectFolder('subfolder/test');
        $this->assertEquals($count + 1, $mail->countMessages());
        $this->assertEquals($mail->getMessage($count + 1)->subject, $message->subject);
        $this->assertEquals($mail->getMessage($count + 1)->from, $message->from);
        $this->assertEquals($mail->getMessage($count + 1)->to, $message->to);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->copyMessage(1, 'justARandomFolder');
    }

    public function testSetFlags()
    {
        $mail = new Storage\Imap($this->_params);

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

        $mail->setFlags(1, array('myflag'));
        $message = $mail->getMessage(1);
        $this->assertFalse($message->hasFlag(Storage::FLAG_SEEN));
        $this->assertFalse($message->hasFlag(Storage::FLAG_FLAGGED));
        $this->assertTrue($message->hasFlag('myflag'));

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->setFlags(1, array(Storage::FLAG_RECENT));
    }

    public function testCapability()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $protocol->login($this->_params['user'], $this->_params['password']);
        $capa = $protocol->capability();
        $this->assertTrue(is_array($capa));
        $this->assertEquals($capa[0], 'CAPABILITY');
    }

    public function testSelect()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $protocol->login($this->_params['user'], $this->_params['password']);
        $status = $protocol->select('INBOX');
        $this->assertTrue(is_array($status['flags']));
        $this->assertEquals($status['exists'], 7);
    }


    public function testExamine()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $protocol->login($this->_params['user'], $this->_params['password']);
        $status = $protocol->examine('INBOX');
        $this->assertTrue(is_array($status['flags']));
        $this->assertEquals($status['exists'], 7);
    }

    public function testClosedSocketNewlineToken()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $protocol->login($this->_params['user'], $this->_params['password']);
        $protocol->logout();

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $protocol->select("foo\nbar");
    }

    public function testEscaping()
    {
        $protocol = new Protocol\Imap();
        $this->assertEquals($protocol->escapeString('foo'), '"foo"');
        $this->assertEquals($protocol->escapeString('f\\oo'), '"f\\\\oo"');
        $this->assertEquals($protocol->escapeString('f"oo'), '"f\\"oo"');
        $this->assertEquals($protocol->escapeString('foo', 'bar'), array('"foo"', '"bar"'));
        $this->assertEquals($protocol->escapeString("f\noo"), array('{4}', "f\noo"));
        $this->assertEquals($protocol->escapeList(array('foo')), '(foo)');
        $this->assertEquals($protocol->escapeList(array(array('foo'))), '((foo))');
        $this->assertEquals($protocol->escapeList(array('foo', 'bar')), '(foo bar)');
    }

    public function testFetch()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $protocol->login($this->_params['user'], $this->_params['password']);
        $protocol->select('INBOX');

        $range = array_combine(range(1, 7), range(1, 7));
        $this->assertEquals($protocol->fetch('UID', 1, INF), $range);
        $this->assertEquals($protocol->fetch('UID', 1, 7), $range);
        $this->assertEquals($protocol->fetch('UID', range(1, 7)), $range);
        $this->assertTrue(is_numeric($protocol->fetch('UID', 1)));

        $result = $protocol->fetch(array('UID', 'FLAGS'), 1, INF);
        foreach ($result as $k => $v) {
            $this->assertEquals($k, $v['UID']);
            $this->assertTrue(is_array($v['FLAGS']));
        }

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $protocol->fetch('UID', 99);
    }

    public function testStore()
    {
        $protocol = new Protocol\Imap($this->_params['host']);
        $protocol->login($this->_params['user'], $this->_params['password']);
        $protocol->select('INBOX');

        $this->assertTrue($protocol->store(array('\Flagged'), 1));
        $this->assertTrue($protocol->store(array('\Flagged'), 1, null, '-'));
        $this->assertTrue($protocol->store(array('\Flagged'), 1, null, '+'));

        $result = $protocol->store(array('\Flagged'), 1, null, '', false);
        $this->assertTrue(in_array('\Flagged', $result[1]));
        $result = $protocol->store(array('\Flagged'), 1, null, '-', false);
        $this->assertFalse(in_array('\Flagged', $result[1]));
        $result = $protocol->store(array('\Flagged'), 1, null, '+', false);
        $this->assertTrue(in_array('\Flagged', $result[1]));
    }

    public function testMove()
    {
        $mail = new Storage\Imap($this->_params);
        $mail->selectFolder('subfolder/test');
        $toCount = $mail->countMessages();
        $mail->selectFolder('INBOX');
        $fromCount = $mail->countMessages();
        $mail->moveMessage(1, 'subfolder/test');


        $this->assertEquals($fromCount - 1, $mail->countMessages());
        $mail->selectFolder('subfolder/test');
        $this->assertEquals($toCount + 1, $mail->countMessages());
    }

    public function testCountFlags()
    {
        $mail = new Storage\Imap($this->_params);
        foreach ($mail as $id => $message) {
            $mail->setFlags($id, array());
        }
        $this->assertEquals($mail->countMessages(Storage::FLAG_SEEN), 0);
        $this->assertEquals($mail->countMessages(Storage::FLAG_ANSWERED), 0);
        $this->assertEquals($mail->countMessages(Storage::FLAG_FLAGGED), 0);

        $mail->setFlags(1, array(Storage::FLAG_SEEN, Storage::FLAG_ANSWERED));
        $mail->setFlags(2, array(Storage::FLAG_SEEN));
        $this->assertEquals($mail->countMessages(Storage::FLAG_SEEN), 2);
        $this->assertEquals($mail->countMessages(Storage::FLAG_ANSWERED), 1);
        $this->assertEquals($mail->countMessages(array(Storage::FLAG_SEEN, Storage::FLAG_ANSWERED)), 1);
        $this->assertEquals($mail->countMessages(array(Storage::FLAG_SEEN, Storage::FLAG_FLAGGED)), 0);
        $this->assertEquals($mail->countMessages(Storage::FLAG_FLAGGED), 0);
    }
}
