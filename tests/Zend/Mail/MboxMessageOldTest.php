<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Storage_Mbox
 */
require_once 'Zend/Mail/Storage/Mbox.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Maildir class, which uses old message class
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Storage_Mbox_OldMessage extends Zend_Mail_Storage_Mbox
{
    /**
     * used message class
     * @var string
     */
    protected $_messageClass = 'Zend_Mail_Message';
}

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MboxMessageOldTest extends PHPUnit_Framework_TestCase
{
    protected $_mboxOriginalFile;
    protected $_mboxFile;
    protected $_tmpdir;

    public function setUp()
    {
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

        $this->_mboxOriginalFile = dirname(__FILE__) . '/_files/test.mbox/INBOX';
        $this->_mboxFile = $this->_tmpdir . 'INBOX';

        copy($this->_mboxOriginalFile, $this->_mboxFile);
    }

    public function tearDown()
    {
        unlink($this->_mboxFile);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Storage_Mbox_OldMessage(array('filename' => $this->_mboxFile));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Storage_Mbox_OldMessage(array('filename' => $this->_mboxFile));

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/

    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Storage_Mbox_OldMessage(array('filename' => $this->_mboxFile));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Storage_Mbox_OldMessage(array('filename' => $this->_mboxFile));

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }


    public function testShortMbox()
    {
        $fh = fopen($this->_mboxFile, 'w');
        fputs($fh, "From \r\nSubject: test\r\nFrom \r\nSubject: test2\r\n");
        fclose($fh);
        $mail = new Zend_Mail_Storage_Mbox_OldMessage(array('filename' => $this->_mboxFile));
        $this->assertEquals($mail->countMessages(), 2);
        $this->assertEquals($mail->getMessage(1)->subject, 'test');
        $this->assertEquals($mail->getMessage(1)->getContent(), '');
        $this->assertEquals($mail->getMessage(2)->subject, 'test2');
        $this->assertEquals($mail->getMessage(2)->getContent(), '');
    }

}
