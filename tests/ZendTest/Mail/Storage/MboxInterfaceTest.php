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
use Zend\Mail\Storage\Message;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class InterfaceTest extends \PHPUnit_Framework_TestCase
{
    protected $_mboxFile;

    public function setUp()
    {
        $this->_mboxFile = __DIR__ . '/../_files/test.mbox/INBOX';
    }

    public function testCount()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $count = count($list);
        $this->assertEquals(7, $count);
    }

    public function testIsset()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $this->assertTrue(isset($list[1]));
    }

    public function testNotIsset()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $this->assertFalse(isset($list[10]));
    }

    public function testArrayGet()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $subject = $list[1]->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testArraySetFail()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $this->setExpectedException('Zend\Mail\Storage\Exception\RuntimeException');
        $list[1] = 'test';
    }

    public function testIterationKey()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $pos = 1;
        foreach ($list as $key => $message) {
            $this->assertEquals($key, $pos, "wrong key in iteration $pos");
            ++$pos;
        }
    }

    public function testIterationIsMessage()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        foreach ($list as $key => $message) {
            $this->assertTrue($message instanceof Message\MessageInterface, 'value in iteration is not a mail message');
        }
    }

    public function testIterationRounds()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $count = 0;
        foreach ($list as $key => $message) {
            ++$count;
        }

        $this->assertEquals(7, $count);
    }

    public function testIterationWithSeek()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $count = 0;
        foreach (new \LimitIterator($list, 1, 3) as $key => $message) {
            ++$count;
        }

        $this->assertEquals(3, $count);
    }

    public function testIterationWithSeekCapped()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $count = 0;
        foreach (new \LimitIterator($list, 3, 7) as $key => $message) {
            ++$count;
        }

        $this->assertEquals(5, $count);
    }

    public function testFallback()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $result = $list->noop();
        $this->assertTrue($result);
    }

    public function testWrongVariable()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $list->thisdoesnotexist;
    }

    public function testGetHeaders()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));
        $headers = $list[1]->getHeaders();
        $this->assertTrue(count($headers) > 0);
    }

    public function testWrongHeader()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        $this->setExpectedException('Zend\Mail\Storage\Exception\InvalidArgumentException');
        $list[1]->thisdoesnotexist;
    }
}
