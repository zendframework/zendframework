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
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Search\Lucene;

use Zend\Search\Lucene;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class PriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $queue = new testPriorityQueueClass();

        $this->assertTrue($queue instanceof Lucene\AbstractPriorityQueue);
    }

    public function testPut()
    {
        $queue = new testPriorityQueueClass();

        $queue->put(1);
        $queue->put(100);
        $queue->put(46);
        $queue->put(347);
        $queue->put(11);
        $queue->put(125);
        $queue->put(-10);
        $queue->put(100);
    }

    public function testPop()
    {
        $queue = new testPriorityQueueClass();

        $queue->put( 1);
        $queue->put( 100);
        $queue->put( 46);
        $queue->put( 347);
        $queue->put( 11);
        $queue->put( 125);
        $queue->put(-10);
        $queue->put( 100);

        $this->assertEquals($queue->pop(), -10);
        $this->assertEquals($queue->pop(),  1  );
        $this->assertEquals($queue->pop(),  11 );
        $this->assertEquals($queue->pop(),  46 );
        $this->assertEquals($queue->pop(),  100);
        $this->assertEquals($queue->pop(),  100);
        $this->assertEquals($queue->pop(),  125);

        $queue->put( 144);
        $queue->put( 546);
        $queue->put( 15);
        $queue->put( 125);
        $queue->put( 325);
        $queue->put(-12);
        $queue->put( 347);

        $this->assertEquals($queue->pop(), -12);
        $this->assertEquals($queue->pop(), 15 );
        $this->assertEquals($queue->pop(), 125);
        $this->assertEquals($queue->pop(), 144);
        $this->assertEquals($queue->pop(), 325);
        $this->assertEquals($queue->pop(), 347);
        $this->assertEquals($queue->pop(), 347);
        $this->assertEquals($queue->pop(), 546);
    }

    public function testClear()
    {
        $queue = new testPriorityQueueClass();

        $queue->put( 1);
        $queue->put( 100);
        $queue->put( 46);
        $queue->put(-10);
        $queue->put( 100);

        $this->assertEquals($queue->pop(), -10);
        $this->assertEquals($queue->pop(),  1  );
        $this->assertEquals($queue->pop(),  46 );

        $queue->clear();
        $this->assertEquals($queue->pop(),  null);

        $queue->put( 144);
        $queue->put( 546);
        $queue->put( 15);

        $this->assertEquals($queue->pop(), 15 );
        $this->assertEquals($queue->pop(), 144);
        $this->assertEquals($queue->pop(), 546);
    }
}


class testPriorityQueueClass extends Lucene\AbstractPriorityQueue
{
    /**
     * Compare elements
     *
     * Returns true, if $el1 is less than $el2; else otherwise
     *
     * @param mixed $el1
     * @param mixed $el2
     * @return boolean
     */
    protected function _less($el1, $el2)
    {
        return ($el1 < $el2);
    }
}
