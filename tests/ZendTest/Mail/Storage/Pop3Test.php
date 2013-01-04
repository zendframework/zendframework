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

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class Pop3Test extends \PHPUnit_Framework_TestCase
{
    protected $_params;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_MAIL_POP3_ENABLED')) {
            $this->markTestSkipped('Zend_Mail POP3 tests are not enabled');
        }

        $this->_params = array('host'     => TESTS_ZEND_MAIL_POP3_HOST,
                               'user'     => TESTS_ZEND_MAIL_POP3_USER,
                               'password' => TESTS_ZEND_MAIL_POP3_PASSWORD);

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
        new Storage\Pop3($this->_params);
    }

    public function testConnectConfig()
    {
         new Storage\Pop3(new Config\Config($this->_params));
    }


    public function testConnectFailure()
    {
        $this->_params['host'] = 'example.example';

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Pop3($this->_params);
    }

    public function testNoParams()
    {
        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Pop3(array());
    }

    public function testConnectSSL()
    {
        if (!TESTS_ZEND_MAIL_POP3_SSL) {
            return;
        }

        $this->_params['ssl'] = 'SSL';

        new Storage\Pop3($this->_params);
    }

    public function testConnectTLS()
    {
        if (!TESTS_ZEND_MAIL_POP3_TLS) {
            return;
        }

        $this->_params['ssl'] = 'TLS';

        new Storage\Pop3($this->_params);
    }

    public function testInvalidService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_POP3_INVALID_PORT;

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Pop3($this->_params);
    }

    public function testWrongService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_POP3_WRONG_PORT;

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        new Storage\Pop3($this->_params);
    }

    public function testClose()
    {
        $mail = new Storage\Pop3($this->_params);

        $mail->close();
    }

    public function testHasTop()
    {
        $mail = new Storage\Pop3($this->_params);

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Storage\Pop3($this->_params);

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Storage\Pop3($this->_params);

        $mail->noop();
    }

    public function testCount()
    {
        $mail = new Storage\Pop3($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(7, $count);
    }

    public function testSize()
    {
        $mail = new Storage\Pop3($this->_params);
        $shouldSizes = array(1 => 397, 89, 694, 452, 497, 101, 139);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Storage\Pop3($this->_params);

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Storage\Pop3($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    public function testFetchTopBody()
    {
        $mail = new Storage\Pop3($this->_params);

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/

    public function testFetchMessageHeader()
    {
        $mail = new Storage\Pop3($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Storage\Pop3($this->_params);

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

/*
    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while deleting message (mbox is read-only)');
    }
*/

    public function testWithInstanceConstruction()
    {
        $protocol = new Protocol\Pop3($this->_params['host']);
        $mail = new Storage\Pop3($protocol);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        // because we did no login this has to throw an exception
        $mail->getMessage(1);
    }

    public function testRequestAfterClose()
    {
        $mail = new Storage\Pop3($this->_params);
        $mail->close();

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getMessage(1);
    }

    public function testServerCapa()
    {
        $mail = new Protocol\Pop3($this->_params['host']);
        $this->assertTrue(is_array($mail->capa()));
    }

    public function testServerUidl()
    {
        $mail = new Protocol\Pop3($this->_params['host']);
        $mail->login($this->_params['user'], $this->_params['password']);

        $uids = $mail->uniqueid();
        $this->assertEquals(count($uids), 7);

        $this->assertEquals($uids[1], $mail->uniqueid(1));
    }

    public function testRawHeader()
    {
        $mail = new Storage\Pop3($this->_params);

        $this->assertTrue(strpos($mail->getRawHeader(1), "\r\nSubject: Simple Message\r\n") > 0);
    }

    public function testUniqueId()
    {
        $mail = new Storage\Pop3($this->_params);

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
        $mail = new Storage\Pop3($this->_params);

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $mail->getNumberByUniqueId('this_is_an_invalid_id');
    }

    public function testReadAfterClose()
    {
        $protocol = new Protocol\Pop3($this->_params['host']);
        $protocol->logout();

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $protocol->readResponse();
    }

    public function testRemove()
    {
        $mail = new Storage\Pop3($this->_params);
        $count = $mail->countMessages();

        $mail->removeMessage(1);
        $this->assertEquals($mail->countMessages(), --$count);

        unset($mail[2]);
        $this->assertEquals($mail->countMessages(), --$count);
    }

    public function testDotMessage()
    {
        $mail = new Storage\Pop3($this->_params);
        $content = '';
        $content .= "Before the dot\r\n";
        $content .= ".\r\n";
        $content .= "is after the dot\r\n";
        $this->assertEquals($mail->getMessage(7)->getContent(), $content);
    }
}
