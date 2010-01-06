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
 * @version    $Id$
 */

/**
 * Zend_Mail_Storage_Maildir
 */
require_once 'Zend/Mail/Storage/Maildir.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class Zend_Mail_MaildirTest extends PHPUnit_Framework_TestCase
{
    protected $_originalMaildir;
    protected $_maildir;
    protected $_tmpdir;

    public function setUp()
    {
        $this->_originalMaildir = dirname(__FILE__) . '/_files/test.maildir/';
        if (!is_dir($this->_originalMaildir . '/cur/')) {
            $this->markTestSkipped('You have to unpack maildir.tar in Zend/Mail/_files/test.maildir/ '
                                 . 'directory before enabling the maildir tests');
            return;
        }

        if ($this->_tmpdir == null) {
            if (TESTS_ZEND_MAIL_TEMPDIR != null) {
                $this->_tmpdir = TESTS_ZEND_MAIL_TEMPDIR;
            } else {
                $this->_tmpdir = dirname(__FILE__) . '/_files/test.tmp/';
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

    public function testLoadOk()
    {
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        } catch (Exception $e) {
            $this->fail('exception raised while loading maildir');
        }
    }

    public function testLoadConfig()
    {
        try {
            $mail = new Zend_Mail_Storage_Maildir(new Zend_Config(array('dirname' => $this->_maildir)));
        } catch (Exception $e) {
            $this->fail('exception raised while loading maildir');
        }
    }

    public function testLoadFailure()
    {
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => '/This/Dir/Does/Not/Exist'));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown dir');
    }

    public function testLoadInvalid()
    {
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => dirname(__FILE__)));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while loading invalid dir');
    }

    public function testClose()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing maildir');
        }
    }

    public function testHasTop()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }

    public function testCount()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        $shouldSizes = array(1 => 397, 89, 694, 452, 497);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/
    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testFetchWrongSize()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->getSize(0);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while getting size for message 0');
    }

    public function testFetchWrongMessageBody()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->getMessage(0);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while fetching message 0');
    }

    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while deleting message (maildir is read-only)');
    }

    public function testHasFlag()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertFalse($mail->getMessage(5)->hasFlag(Zend_Mail_Storage::FLAG_SEEN));
        $this->assertTrue($mail->getMessage(5)->hasFlag(Zend_Mail_Storage::FLAG_RECENT));
        $this->assertTrue($mail->getMessage(2)->hasFlag(Zend_Mail_Storage::FLAG_FLAGGED));
        $this->assertFalse($mail->getMessage(2)->hasFlag(Zend_Mail_Storage::FLAG_ANSWERED));
    }

    public function testGetFlags()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $flags = $mail->getMessage(1)->getFlags();
        $this->assertTrue(isset($flags[Zend_Mail_Storage::FLAG_SEEN]));
        $this->assertTrue(in_array(Zend_Mail_Storage::FLAG_SEEN, $flags));
    }

    public function testUniqueId()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertTrue($mail->hasUniqueId);
        $this->assertEquals(1, $mail->getNumberByUniqueId($mail->getUniqueId(1)));

        $ids = $mail->getUniqueId();
        $should_ids = array(1 => '1000000000.P1.example.org', '1000000001.P1.example.org', '1000000002.P1.example.org',
                            '1000000003.P1.example.org', '1000000004.P1.example.org');
        foreach ($ids as $num => $id) {
            $this->assertEquals($id, $should_ids[$num]);

            if ($mail->getNumberByUniqueId($id) != $num) {
                    $this->fail('reverse lookup failed');
            }
        }
    }

    public function testWrongUniqueId()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        try {
            $mail->getNumberByUniqueId('this_is_an_invalid_id');
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while getting number for invalid id');
    }

    public function isFileTest($dir)
    {
        if (file_exists($this->_maildir . '/' . $dir)) {
            rename($this->_maildir . '/' . $dir, $this->_maildir . '/' . $dir . 'bak');
        }
        touch($this->_maildir . '/' . $dir);

        $check = false;
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        } catch (Exception $e) {
            $check = true;
            // test ok
        }

        unlink($this->_maildir . '/' . $dir);
        if (file_exists($this->_maildir . '/' . $dir . 'bak')) {
            rename($this->_maildir . '/' . $dir . 'bak', $this->_maildir . '/' . $dir);
        }

        if (!$check) {
           $this->fail('no exception while loading invalid dir with ' . $dir . ' as file');
        }
    }

    public function testCurIsFile()
    {
        $this->isFileTest('cur');
    }

    public function testNewIsFile()
    {
        $this->isFileTest('new');
    }

    public function testTmpIsFile()
    {
        $this->isFileTest('tmp');
    }

    public function notReadableTest($dir)
    {
        $stat = stat($this->_maildir . '/' . $dir);
        chmod($this->_maildir . '/' . $dir, 0);

        $check = false;
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        } catch (Exception $e) {
            $check = true;
            // test ok
        }

        chmod($this->_maildir . '/' . $dir, $stat['mode']);

        if (!$check) {
           $this->fail('no exception while loading invalid dir with ' . $dir . ' not readable');
        }
    }

    public function testNotReadableCur()
    {
        $this->notReadableTest('cur');
    }

    public function testNotReadableNew()
    {
        $this->notReadableTest('new');
    }

    public function testCountFlags()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        $this->assertEquals($mail->countMessages(Zend_Mail_Storage::FLAG_DELETED), 0);
        $this->assertEquals($mail->countMessages(Zend_Mail_Storage::FLAG_RECENT), 1);
        $this->assertEquals($mail->countMessages(Zend_Mail_Storage::FLAG_FLAGGED), 1);
        $this->assertEquals($mail->countMessages(Zend_Mail_Storage::FLAG_SEEN), 4);
        $this->assertEquals($mail->countMessages(array(Zend_Mail_Storage::FLAG_SEEN, Zend_Mail_Storage::FLAG_FLAGGED)), 1);
        $this->assertEquals($mail->countMessages(array(Zend_Mail_Storage::FLAG_SEEN, Zend_Mail_Storage::FLAG_RECENT)), 0);
    }

    public function testFetchPart()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        $this->assertEquals($mail->getMessage(4)->getPart(2)->contentType, 'text/x-vertical');
    }

    public function testPartSize()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        $this->assertEquals($mail->getMessage(4)->getPart(2)->getSize(), 88);
    }

    public function testSizePlusPlus()
    {
        rename($this->_maildir . '/cur/1000000000.P1.example.org:2,S', $this->_maildir . '/cur/1000000000.P1.example.org,S=123:2,S');
        rename($this->_maildir . '/cur/1000000001.P1.example.org:2,FS', $this->_maildir . '/cur/1000000001.P1.example.org,S=456:2,FS');
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        $shouldSizes = array(1 => 123, 456, 694, 452, 497);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSizePlusPlus()
    {
        rename($this->_maildir . '/cur/1000000001.P1.example.org:2,FS', $this->_maildir . '/cur/1000000001.P1.example.org,S=456:2,FS');
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $size = $mail->getSize(2);
        $this->assertEquals(456, $size);
    }

}
