<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_PriorityQueue
 */
require_once 'Zend/Search/Lucene/PriorityQueue.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_PriorityQueueTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $queue = new Zend_Search_Lucene_PriorityQueue_testClass();

        $this->assertTrue($queue instanceof Zend_Search_Lucene_PriorityQueue);
    }

    public function testPut()
    {
        $queue = new Zend_Search_Lucene_PriorityQueue_testClass();

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
        $queue = new Zend_Search_Lucene_PriorityQueue_testClass();

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
        $queue = new Zend_Search_Lucene_PriorityQueue_testClass();

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


class Zend_Search_Lucene_PriorityQueue_testClass extends Zend_Search_Lucene_PriorityQueue
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
