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

use Zend\Mail\Storage;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

        try {
            $list[1] = 'test';
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception thrown while writing to array access');
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
            $this->assertTrue($message instanceof \Zend\Mail\MailMessage, 'value in iteration is not a mail message');
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

        try {
            $result = $list->noop();
            $this->assertTrue($result);
        } catch (\Exception $e) {
            $this->fail('exception raised while calling noop thru fallback');
        }
    }

    public function testWrongVariable()
    {
        $list = new Storage\Mbox(array('filename' => $this->_mboxFile));

        try {
            $list->thisdoesnotexist;
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception thrown while reading wrong variable (via __get())');
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

        try {
            $list[1]->thisdoesnotexist;
        } catch (\Exception $e) {
            return; // test ok
        }

        $this->fail('no exception thrown while reading wrong header');
    }
}
