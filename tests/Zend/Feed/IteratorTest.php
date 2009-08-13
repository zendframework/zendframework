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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Feed_IteratorTest extends PHPUnit_Framework_TestCase
{
    private $_feed;
    private $_nsfeed;

    public function setUp()
    {
        $this->_feed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeed.xml');
        $this->_nsfeed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeedNamespaced.xml');
    }

    public function testRewind()
    {
        $times = 0;
        foreach ($this->_feed as $f) {
            ++$times;
        }

        $times2 = 0;
        foreach ($this->_feed as $f) {
            ++$times2;
        }

        $this->assertEquals($times, $times2, 'Feed should have the same number of iterations multiple times through');

        $times = 0;
        foreach ($this->_nsfeed as $f) {
            ++$times;
        }

        $times2 = 0;
        foreach ($this->_nsfeed as $f) {
            ++$times2;
        }

        $this->assertEquals($times, $times2, 'Feed should have the same number of iterations multiple times through');
    }

    public function testCurrent()
    {
        foreach ($this->_feed as $f) {
            $this->assertType('Zend_Feed_Entry_Atom', $f, 'Each feed entry should be an instance of Zend_Feed_Entry_Atom');
            break;
        }

        foreach ($this->_nsfeed as $f) {
            $this->assertType('Zend_Feed_Entry_Atom', $f, 'Each feed entry should be an instance of Zend_Feed_Entry_Atom');
            break;
        }
    }

    public function testKey()
    {
        $keys = array();
        foreach ($this->_feed as $k => $f) {
            $keys[] = $k;
        }
        $this->assertEquals($keys, array(0, 1), 'Feed should have keys 0 and 1');

        $keys = array();
        foreach ($this->_nsfeed as $k => $f) {
            $keys[] = $k;
        }
        $this->assertEquals($keys, array(0, 1), 'Feed should have keys 0 and 1');
    }

    public function testNext()
    {
        $last = null;
        foreach ($this->_feed as $current) {
            $this->assertFalse($last === $current, 'Iteration should produce a new object each entry');
            $last = $current;
        }

        $last = null;
        foreach ($this->_nsfeed as $current) {
            $this->assertFalse($last === $current, 'Iteration should produce a new object each entry');
            $last = $current;
        }
    }

}
