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
class ElasticTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Paginator\ScrollingStyle\Elastic
     */
    private $scrollingStyle;
    /**
     * @var \Zend\Paginator\Paginator
     */
    private $paginator;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->scrollingStyle = new \Zend\Paginator\ScrollingStyle\Elastic();
        $this->paginator = new Paginator(new ArrayAdapter(range(1, 101)));
        $this->paginator->setItemCountPerPage(5);
        $this->paginator->setPageRange(5);
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
        $expected = array_combine(range(1, 5), range(1, 5));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsPagesInRangeForSecondPage()
    {
        $this->paginator->setCurrentPageNumber(2);
        $actual = $this->scrollingStyle->getPages($this->paginator);
        $expected = array_combine(range(1, 6), range(1, 6));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsPagesInRangeForTenthPage()
    {
        $this->paginator->setCurrentPageNumber(10);
        $actual = $this->scrollingStyle->getPages($this->paginator);
        $expected = array_combine(range(6, 14), range(6, 14));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsPagesInRangeForLastPage()
    {
        $this->paginator->setCurrentPageNumber(21);
        $actual = $this->scrollingStyle->getPages($this->paginator);
        $expected = array_combine(range(17, 21), range(17, 21));
        $this->assertEquals($expected, $actual);
    }

    public function testGetsNextAndPreviousPageForFirstPage()
    {
        $this->paginator->setCurrentPageNumber(1);
        $pages = $this->paginator->getPages('Elastic');

        $this->assertEquals(2, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondPage()
    {
        $this->paginator->setCurrentPageNumber(2);
        $pages = $this->paginator->getPages('Elastic');
        $this->assertEquals(1, $pages->previous);
        $this->assertEquals(3, $pages->next);
    }

    public function testGetsNextAndPreviousPageForMiddlePage()
    {
        $this->paginator->setCurrentPageNumber(10);
        $pages = $this->paginator->getPages('Elastic');
        $this->assertEquals(9, $pages->previous);
        $this->assertEquals(11, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondLastPage()
    {
        $this->paginator->setCurrentPageNumber(20);
        $pages = $this->paginator->getPages('Elastic');
        $this->assertEquals(19, $pages->previous);
        $this->assertEquals(21, $pages->next);
    }

    public function testGetsNextAndPreviousPageForLastPage()
    {
        $this->paginator->setCurrentPageNumber(21);
        $pages = $this->paginator->getPages('Elastic');
        $this->assertEquals(20, $pages->previous);
    }

    public function testNoPagesOnLastPageEqualsPageRange()
    {
        $this->paginator->setPageRange(3);
        $this->paginator->setCurrentPageNumber(21);
        $pages = $this->paginator->getPages('Elastic');
        $this->assertEquals(3, count($pages->pagesInRange));
    }

    public function testNoPagesOnSecondLastPageEqualsPageRangeMinOne()
    {
        $this->paginator->setPageRange(3);
        $this->paginator->setCurrentPageNumber(20);
        $pages = $this->paginator->getPages('Elastic');
        $this->assertEquals(4, count($pages->pagesInRange));
    }

    public function testNoPagesBeforeSecondLastPageEqualsPageRangeMinTwo()
    {
        $this->paginator->setPageRange(3);
        $this->paginator->setCurrentPageNumber(19);
        $pages = $this->paginator->getPages('Elastic');
        $this->assertEquals(5, count($pages->pagesInRange));
    }
}
