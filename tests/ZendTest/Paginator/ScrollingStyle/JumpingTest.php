<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\ScrollingStyle;

use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class JumpingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Paginator\ScrollingStyle\Jumping
     */
    private $scrollingStyle;
    /**
     * @var \Zend\Paginator\Paginator
     */
    private $paginator;

    private $expectedRange;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->scrollingStyle = new \Zend\Paginator\ScrollingStyle\Jumping();
        $this->paginator = new Paginator(new ArrayAdapter(range(1, 101)));
        $this->paginator->setItemCountPerPage(10);
        $this->paginator->setPageRange(10);
        $this->expectedRange = array_combine(range(1, 10), range(1, 10));
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->scrollingStyle = null;
        $this->paginator = null;
        parent::tearDown();
    }

    public function testGetsPagesInRangeForFirstPage()
    {
        $this->paginator->setCurrentPageNumber(1);
        $actual = $this->scrollingStyle->getPages($this->paginator);
        $this->assertEquals($this->expectedRange, $actual);
    }

    public function testGetsPagesInRangeForSecondPage()
    {
        $this->paginator->setCurrentPageNumber(2);
        $actual = $this->scrollingStyle->getPages($this->paginator);
        $this->assertEquals($this->expectedRange, $actual);
    }

    public function testGetsPagesInRangeForSecondLastPage()
    {
        $this->paginator->setCurrentPageNumber(10);
        $actual = $this->scrollingStyle->getPages($this->paginator);
        $this->assertEquals($this->expectedRange, $actual);
    }

    public function testGetsPagesInRangeForLastPage()
    {
        $this->paginator->setCurrentPageNumber(11);
        $actual = $this->scrollingStyle->getPages($this->paginator);
        $expected = array(11 => 11);
        $this->assertEquals($expected, $actual);
    }

    public function testGetsNextAndPreviousPageForFirstPage()
    {
        $this->paginator->setCurrentPageNumber(1);
        $pages = $this->paginator->getPages('Jumping');

        $this->assertEquals(2, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondPage()
    {
        $this->paginator->setCurrentPageNumber(2);
        $pages = $this->paginator->getPages('Jumping');
        $this->assertEquals(1, $pages->previous);
        $this->assertEquals(3, $pages->next);
    }

    public function testGetsNextAndPreviousPageForMiddlePage()
    {
        $this->paginator->setCurrentPageNumber(6);
        $pages = $this->paginator->getPages('Jumping');
        $this->assertEquals(5, $pages->previous);
        $this->assertEquals(7, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondLastPage()
    {
        $this->paginator->setCurrentPageNumber(10);
        $pages = $this->paginator->getPages('Jumping');
        $this->assertEquals(9, $pages->previous);
        $this->assertEquals(11, $pages->next);
    }

    public function testGetsNextAndPreviousPageForLastPage()
    {
        $this->paginator->setCurrentPageNumber(11);
        $pages = $this->paginator->getPages('Jumping');
        $this->assertEquals(10, $pages->previous);
    }
}
