<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator;

use ReflectionMethod;
use stdClass;
use Zend\Cache\StorageFactory as CacheFactory;
use Zend\Config;
use Zend\Db\Adapter as DbAdapter;
use Zend\Db\Sql;
use Zend\Filter;
use Zend\Paginator;
use Zend\Paginator\Adapter;
use Zend\Paginator\Exception;
use Zend\View;
use Zend\View\Helper;
use ZendTest\Paginator\TestAsset\TestArrayAggregate;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class PaginatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Paginator instance
     *
     * @var Paginator\Paginator
     */
    protected $paginator = null;

    protected $testCollection = null;

    protected $cache;
    protected $cacheDir;

    protected $select = null;

    protected $config = null;

    /**
     * @var DbAdapter\Adapter
     */
    protected $adapter = null;

    protected function setUp()
    {
        $this->select = new Sql\Select;
        $this->select->from('test');

        $this->testCollection = range(1, 101);
        $this->paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter($this->testCollection));

        $this->config = Config\Factory::fromFile(__DIR__ . '/_files/config.xml', true);

        $this->cache = CacheFactory::adapterFactory('memory', array('memory_limit' => 0));
        Paginator\Paginator::setCache($this->cache);

        $this->_restorePaginatorDefaults();
    }

    protected function tearDown()
    {
        $this->testCollection = null;
        $this->paginator = null;
    }

    protected function _getTmpDir()
    {
        $tmpDir = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'zend_paginator';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir);
        }
        $this->cacheDir = $tmpDir;

        return $tmpDir;
    }

    protected function _rmDirRecursive($path)
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $file) {
            if (!$file->isDir()) {
                unlink($file->getPathname());
            } elseif (!in_array($file->getFilename(), array('.', '..'))) {
                $this->_rmDirRecursive($file->getPathname());
            }
        }
        unset($file, $dir); // required on windows to remove file handle
        if (!rmdir($path)) {
            throw new Exception\RuntimeException('Unable to remove temporary directory ' . $path
                                . '; perhaps it has a nested structure?');
        }
    }

    protected function _restorePaginatorDefaults()
    {
        $this->paginator->setItemCountPerPage(10);
        $this->paginator->setCurrentPageNumber(1);
        $this->paginator->setPageRange(10);
        $this->paginator->setView();

        Paginator\Paginator::setDefaultScrollingStyle();
        Helper\PaginationControl::setDefaultViewPartial(null);

        Paginator\Paginator::setGlobalConfig($this->config->default);

        Paginator\Paginator::setScrollingStylePluginManager(new Paginator\ScrollingStylePluginManager());

        $this->paginator->setCacheEnabled(true);
    }

    public function testGetsAndSetsDefaultScrollingStyle()
    {
        $this->assertEquals(Paginator\Paginator::getDefaultScrollingStyle(), 'Sliding');
        Paginator\Paginator::setDefaultScrollingStyle('Scrolling');
        $this->assertEquals(Paginator\Paginator::getDefaultScrollingStyle(), 'Scrolling');
        Paginator\Paginator::setDefaultScrollingStyle('Sliding');
    }

    public function testHasCorrectCountAfterInit()
    {
        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 101)));
        $this->assertEquals(11, $paginator->count());
    }

    public function testHasCorrectCountOfAllItemsAfterInit()
    {
        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 101)));
        $this->assertEquals(101, $paginator->getTotalItemCount());
    }

    public function testLoadsFromConfig()
    {
        Paginator\Paginator::setGlobalConfig($this->config->testing);
        $this->assertEquals('Scrolling', Paginator\Paginator::getDefaultScrollingStyle());

        $plugins = Paginator\Paginator::getScrollingStylePluginManager();
        $this->assertInstanceOf('ZendTest\Paginator\TestAsset\ScrollingStylePluginManager', $plugins);

        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 101)));
        $this->assertEquals(3, $paginator->getItemCountPerPage());
        $this->assertEquals(7, $paginator->getPageRange());
    }

    public function testGetsPagesForPageOne()
    {
        $expected = new stdClass();
        $expected->pageCount        = 11;
        $expected->itemCountPerPage = 10;
        $expected->first            = 1;
        $expected->current          = 1;
        $expected->last             = 11;
        $expected->next             = 2;
        $expected->pagesInRange     = array_combine(range(1, 10), range(1, 10));
        $expected->firstPageInRange = 1;
        $expected->lastPageInRange  = 10;
        $expected->currentItemCount = 10;
        $expected->totalItemCount   = 101;
        $expected->firstItemNumber  = 1;
        $expected->lastItemNumber   = 10;

        $actual = $this->paginator->getPages();

        $this->assertEquals($expected, $actual);
    }

    public function testGetsPagesForPageTwo()
    {
        $expected = new stdClass();
        $expected->pageCount        = 11;
        $expected->itemCountPerPage = 10;
        $expected->first            = 1;
        $expected->current          = 2;
        $expected->last             = 11;
        $expected->previous         = 1;
        $expected->next             = 3;
        $expected->pagesInRange     = array_combine(range(1, 10), range(1, 10));
        $expected->firstPageInRange = 1;
        $expected->lastPageInRange  = 10;
        $expected->currentItemCount = 10;
        $expected->totalItemCount   = 101;
        $expected->firstItemNumber  = 11;
        $expected->lastItemNumber   = 20;

        $this->paginator->setCurrentPageNumber(2);
        $actual = $this->paginator->getPages();

        $this->assertEquals($expected, $actual);
    }

    public function testRendersWithoutPartial()
    {
        $this->paginator->setView(new View\Renderer\PhpRenderer());
        $string = @$this->paginator->__toString();
        $this->assertEquals('', $string);
    }

    public function testRendersWithPartial()
    {
        $view = new View\Renderer\PhpRenderer();
        $view->resolver()->addPath(__DIR__ . '/_files/scripts');

        Helper\PaginationControl::setDefaultViewPartial('partial.phtml');

        $this->paginator->setView($view);

        $string = $this->paginator->__toString();
        $this->assertEquals('partial rendered successfully', $string);
    }

    public function testGetsPageCount()
    {
        $this->assertEquals(11, $this->paginator->count());
    }

    public function testGetsAndSetsItemCountPerPage()
    {
        Paginator\Paginator::setGlobalConfig(new Config\Config(array()));
        $this->paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 101)));
        $this->assertEquals(10, $this->paginator->getItemCountPerPage());
        $this->paginator->setItemCountPerPage(15);
        $this->assertEquals(15, $this->paginator->getItemCountPerPage());
        $this->paginator->setItemCountPerPage(0);
        $this->assertEquals(101, $this->paginator->getItemCountPerPage());
        $this->paginator->setItemCountPerPage(10);
    }

    /**
     * @group ZF-5376
     */
    public function testGetsAndSetsItemCounterPerPageOfNegativeOne()
    {
        Paginator\Paginator::setGlobalConfig(new Config\Config(array()));
        $this->paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter(range(1, 101)));
        $this->paginator->setItemCountPerPage(-1);
        $this->assertEquals(101, $this->paginator->getItemCountPerPage());
        $this->paginator->setItemCountPerPage(10);
    }

    /**
     * @group ZF-5376
     */
    public function testGetsAndSetsItemCounterPerPageOfZero()
    {
        Paginator\Paginator::setGlobalConfig(new Config\Config(array()));
        $this->paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter(range(1, 101)));
        $this->paginator->setItemCountPerPage(0);
        $this->assertEquals(101, $this->paginator->getItemCountPerPage());
        $this->paginator->setItemCountPerPage(10);
    }

    /**
     * @group ZF-5376
     */
    public function testGetsAndSetsItemCounterPerPageOfNull()
    {
        Paginator\Paginator::setGlobalConfig(new Config\Config(array()));
        $this->paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter(range(1, 101)));
        $this->paginator->setItemCountPerPage();
        $this->assertEquals(101, $this->paginator->getItemCountPerPage());
        $this->paginator->setItemCountPerPage(10);
    }

    public function testGetsCurrentItemCount()
    {
        $this->paginator->setItemCountPerPage(10);
        $this->paginator->setPageRange(10);

        $this->assertEquals(10, $this->paginator->getCurrentItemCount());

        $this->paginator->setCurrentPageNumber(11);

        $this->assertEquals(1, $this->paginator->getCurrentItemCount());

        $this->paginator->setCurrentPageNumber(1);
    }

    public function testGetsCurrentItems()
    {
        $items = $this->paginator->getCurrentItems();
        $this->assertInstanceOf('ArrayIterator', $items);

        $count = 0;

        foreach ($items as $item) {
            $count++;
        }

        $this->assertEquals(10, $count);
    }

    public function testGetsIterator()
    {
        $items = $this->paginator->getIterator();
        $this->assertInstanceOf('ArrayIterator', $items);

        $count = 0;

        foreach ($items as $item) {
            $count++;
        }

        $this->assertEquals(10, $count);
    }

    public function testGetsAndSetsCurrentPageNumber()
    {
        $this->assertEquals(1, $this->paginator->getCurrentPageNumber());
        $this->paginator->setCurrentPageNumber(-1);
        $this->assertEquals(1, $this->paginator->getCurrentPageNumber());
        $this->paginator->setCurrentPageNumber(11);
        $this->assertEquals(11, $this->paginator->getCurrentPageNumber());
        $this->paginator->setCurrentPageNumber(111);
        $this->assertEquals(11, $this->paginator->getCurrentPageNumber());
        $this->paginator->setCurrentPageNumber(1);
        $this->assertEquals(1, $this->paginator->getCurrentPageNumber());
    }

    public function testGetsAbsoluteItemNumber()
    {
        $this->assertEquals(1, $this->paginator->getAbsoluteItemNumber(1));
        $this->assertEquals(11, $this->paginator->getAbsoluteItemNumber(1, 2));
        $this->assertEquals(24, $this->paginator->getAbsoluteItemNumber(4, 3));
    }

    public function testGetsItem()
    {
        $this->assertEquals(1, $this->paginator->getItem(1));
        $this->assertEquals(11, $this->paginator->getItem(1, 2));
        $this->assertEquals(24, $this->paginator->getItem(4, 3));
    }

    public function testThrowsExceptionWhenCollectionIsEmpty()
    {
        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(array()));

        $this->setExpectedException('Zend\Paginator\Exception\InvalidArgumentException', 'Page 1 does not exist');
        $paginator->getItem(1);
    }

    public function testThrowsExceptionWhenRetrievingNonexistentItemFromLastPage()
    {
        $this->setExpectedException('Zend\Paginator\Exception\InvalidArgumentException', 'Page 11 does not contain item number 10');
        $this->paginator->getItem(10, 11);
    }

    public function testNormalizesPageNumber()
    {
        $this->assertEquals(1, $this->paginator->normalizePageNumber(0));
        $this->assertEquals(1, $this->paginator->normalizePageNumber(1));
        $this->assertEquals(2, $this->paginator->normalizePageNumber(2));
        $this->assertEquals(5, $this->paginator->normalizePageNumber(5));
        $this->assertEquals(10, $this->paginator->normalizePageNumber(10));
        $this->assertEquals(11, $this->paginator->normalizePageNumber(11));
        $this->assertEquals(11, $this->paginator->normalizePageNumber(12));
    }

    public function testNormalizesItemNumber()
    {
        $this->assertEquals(1, $this->paginator->normalizeItemNumber(0));
        $this->assertEquals(1, $this->paginator->normalizeItemNumber(1));
        $this->assertEquals(2, $this->paginator->normalizeItemNumber(2));
        $this->assertEquals(5, $this->paginator->normalizeItemNumber(5));
        $this->assertEquals(9, $this->paginator->normalizeItemNumber(9));
        $this->assertEquals(10, $this->paginator->normalizeItemNumber(10));
        $this->assertEquals(10, $this->paginator->normalizeItemNumber(11));
    }

    /**
     * @group ZF-8656
     */
    public function testNormalizesPageNumberWhenGivenAFloat()
    {
        $this->assertEquals(1, $this->paginator->normalizePageNumber(0.5));
        $this->assertEquals(1, $this->paginator->normalizePageNumber(1.99));
        $this->assertEquals(2, $this->paginator->normalizePageNumber(2.3));
        $this->assertEquals(5, $this->paginator->normalizePageNumber(5.1));
        $this->assertEquals(10, $this->paginator->normalizePageNumber(10.06));
        $this->assertEquals(11, $this->paginator->normalizePageNumber(11.5));
        $this->assertEquals(11, $this->paginator->normalizePageNumber(12.7889));
    }

    /**
     * @group ZF-8656
     */
    public function testNormalizesItemNumberWhenGivenAFloat()
    {
        $this->assertEquals(1, $this->paginator->normalizeItemNumber(0.5));
        $this->assertEquals(1, $this->paginator->normalizeItemNumber(1.99));
        $this->assertEquals(2, $this->paginator->normalizeItemNumber(2.3));
        $this->assertEquals(5, $this->paginator->normalizeItemNumber(5.1));
        $this->assertEquals(9, $this->paginator->normalizeItemNumber(9.06));
        $this->assertEquals(10, $this->paginator->normalizeItemNumber(10.5));
        $this->assertEquals(10, $this->paginator->normalizeItemNumber(11.7889));
    }

    public function testGetsPagesInSubsetRange()
    {
        $actual = $this->paginator->getPagesInRange(3, 8);
        $this->assertEquals(array_combine(range(3, 8), range(3, 8)), $actual);
    }

    public function testGetsPagesInOutOfBoundsRange()
    {
        $actual = $this->paginator->getPagesInRange(-1, 12);
        $this->assertEquals(array_combine(range(1, 11), range(1, 11)), $actual);
    }

    public function testGetsItemsByPage()
    {
        $expected = new \ArrayIterator(range(1, 10));

        $page1 = $this->paginator->getItemsByPage(1);

        $this->assertEquals($page1, $expected);
        $this->assertEquals($page1, $this->paginator->getItemsByPage(1));
    }

    public function testGetsItemCount()
    {
        $this->assertEquals(101, $this->paginator->getItemCount(range(1, 101)));

        $limitIterator = new \LimitIterator(new \ArrayIterator(range(1, 101)));
        $this->assertEquals(101, $this->paginator->getItemCount($limitIterator));
    }

    public function testGeneratesViewIfNonexistent()
    {
        $this->assertInstanceOf('Zend\\View\\Renderer\\RendererInterface', $this->paginator->getView());
    }

    public function testGetsAndSetsView()
    {
        $this->paginator->setView(new View\Renderer\PhpRenderer());
        $this->assertInstanceOf('Zend\\View\\Renderer\\RendererInterface', $this->paginator->getView());
    }

    public function testRenders()
    {
        $this->setExpectedException('Zend\\View\\Exception\\ExceptionInterface', 'view partial');
        $this->paginator->render(new View\Renderer\PhpRenderer());
    }

    public function testGetsAndSetsPageRange()
    {
        $this->assertEquals(10, $this->paginator->getPageRange());
        $this->paginator->setPageRange(15);
        $this->assertEquals(15, $this->paginator->getPageRange());
    }

    /**
     * @group ZF-3720
     */
    public function testGivesCorrectItemCount()
    {
        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 101)));
        $paginator->setCurrentPageNumber(5)
                  ->setItemCountPerPage(5);
        $expected = new \ArrayIterator(range(21, 25));

        $this->assertEquals($expected, $paginator->getCurrentItems());
    }

    /**
     * @group ZF-3737
     */
    public function testKeepsCurrentPageNumberAfterItemCountPerPageSet()
    {
        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(array('item1', 'item2')));
        $paginator->setCurrentPageNumber(2)
                  ->setItemCountPerPage(1);

        $items = $paginator->getCurrentItems();

        $this->assertEquals('item2', $items[0]);
    }

    /**
     * @group ZF-4193
     */
    public function testCastsIntegerValuesToInteger()
    {
        // Current page number
        $this->paginator->setCurrentPageNumber(3.3);
        $this->assertTrue($this->paginator->getCurrentPageNumber() == 3);

        // Item count per page
        $this->paginator->setItemCountPerPage(3.3);
        $this->assertTrue($this->paginator->getItemCountPerPage() == 3);

        // Page range
        $this->paginator->setPageRange(3.3);
        $this->assertTrue($this->paginator->getPageRange() == 3);
    }

    /**
     * @group ZF-4207
     */
    public function testAcceptsTraversableInstanceFromAdapter()
    {
        $paginator = new Paginator\Paginator(new TestAsset\TestAdapter());
        $this->assertInstanceOf('ArrayObject', $paginator->getCurrentItems());
    }

    public function testCachedItem()
    {
        $this->paginator->setCurrentPageNumber(1)->getCurrentItems();
        $this->paginator->setCurrentPageNumber(2)->getCurrentItems();
        $this->paginator->setCurrentPageNumber(3)->getCurrentItems();

        $pageItems = $this->paginator->getPageItemCache();
        $expected = array(
           1 => new \ArrayIterator(range(1, 10)),
           2 => new \ArrayIterator(range(11, 20)),
           3 => new \ArrayIterator(range(21, 30))
        );
        $this->assertEquals($expected, $pageItems);
    }

    public function testClearPageItemCache()
    {
        $this->paginator->setCurrentPageNumber(1)->getCurrentItems();
        $this->paginator->setCurrentPageNumber(2)->getCurrentItems();
        $this->paginator->setCurrentPageNumber(3)->getCurrentItems();

        // clear only page 2 items
        $this->paginator->clearPageItemCache(2);
        $pageItems = $this->paginator->getPageItemCache();
        $expected = array(
           1 => new \ArrayIterator(range(1, 10)),
           3 => new \ArrayIterator(range(21, 30))
        );
        $this->assertEquals($expected, $pageItems);

        // clear all
        $this->paginator->clearPageItemCache();
        $pageItems = $this->paginator->getPageItemCache();
        $this->assertEquals(array(), $pageItems);
    }

    public function testWithCacheDisabled()
    {
        $this->paginator->setCacheEnabled(false);
        $this->paginator->setCurrentPageNumber(1)->getCurrentItems();

        $cachedPageItems = $this->paginator->getPageItemCache();
        $expected = new \ArrayIterator(range(1, 10));

        $this->assertEquals(array(), $cachedPageItems);

        $pageItems = $this->paginator->getCurrentItems();

        $this->assertEquals($expected, $pageItems);
    }

    public function testCacheDoesNotDisturbResultsWhenChangingParam()
    {
        $this->paginator->setCurrentPageNumber(1)->getCurrentItems();
        $pageItems = $this->paginator->setItemCountPerPage(5)->getCurrentItems();

        $expected = new \ArrayIterator(range(1, 5));
        $this->assertEquals($expected, $pageItems);

        $pageItems = $this->paginator->getItemsByPage(2);
        $expected = new \ArrayIterator(range(6, 10));
        $this->assertEquals($expected, $pageItems);

        // change the inside Paginator scale
        $pageItems = $this->paginator->setItemCountPerPage(8)->setCurrentPageNumber(3)->getCurrentItems();

        $pageItems = $this->paginator->getPageItemCache();
        $expected = /*array(3 => */ new \ArrayIterator(range(17, 24)) /*) */;
        $this->assertEquals($expected, $pageItems[3]);

        // get back to already cached data
        $this->paginator->setItemCountPerPage(5);
        $pageItems = $this->paginator->getPageItemCache();
        $expected =array(1 => new \ArrayIterator(range(1, 5)),
                         2 => new \ArrayIterator(range(6, 10)));
        $this->assertEquals($expected[1], $pageItems[1]);
        $this->assertEquals($expected[2], $pageItems[2]);
    }

    public function testToJson()
    {
        $this->paginator->setCurrentPageNumber(1);

        $json = $this->paginator->toJson();

        $expected = '"0":1,"1":2,"2":3,"3":4,"4":5,"5":6,"6":7,"7":8,"8":9,"9":10';

        $this->assertContains($expected, $json);
    }

    // ZF-5519
    public function testFilter()
    {
        $filter = new Filter\Callback(array($this, 'filterCallback'));
        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 101)));
        $paginator->setFilter($filter);

        $page = $paginator->getCurrentItems();

        $this->assertEquals(new \ArrayIterator(range(10, 100, 10)), $page);
    }

    public function filterCallback($value)
    {
        $data = array();

        foreach ($value as $number) {
            $data[] = ($number * 10);
        }

        return $data;
    }

    /**
     * @group ZF-5785
     */
    public function testGetSetDefaultItemCountPerPage()
    {
        Paginator\Paginator::setGlobalConfig(new Config\Config(array()));

        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 10)));
        $this->assertEquals(10, $paginator->getItemCountPerPage());

        Paginator\Paginator::setDefaultItemCountPerPage(20);
        $this->assertEquals(20, Paginator\Paginator::getDefaultItemCountPerPage());

        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 10)));
        $this->assertEquals(20, $paginator->getItemCountPerPage());

        $this->_restorePaginatorDefaults();
    }

    /**
     * @group ZF-7207
     */
    public function testItemCountPerPageByDefault()
    {
        $paginator = new Paginator\Paginator(new Adapter\ArrayAdapter(range(1, 20)));
        $this->assertEquals(2, $paginator->count());
    }

    /**
     * @group ZF-5427
     */
    public function testNegativeItemNumbers()
    {
        $this->assertEquals(10, $this->paginator->getItem(-1, 1));
        $this->assertEquals(9, $this->paginator->getItem(-2, 1));
        $this->assertEquals(101, $this->paginator->getItem(-1, -1));
    }

    /**
     * @group ZF-7602
     */
    public function testAcceptAndHandlePaginatorAdapterAggregateDataInFactory()
    {
        $p = new Paginator\Paginator(new TestArrayAggregate());

        $this->assertEquals(1, count($p));
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $p->getAdapter());
        $this->assertEquals(4, count($p->getAdapter()));
    }

    /**
     * @group ZF-7602
     */
    public function testAcceptAndHandlePaginatorAdapterAggregateInConstructor()
    {
        $p = new Paginator\Paginator(new TestArrayAggregate());

        $this->assertEquals(1, count($p));
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $p->getAdapter());
        $this->assertEquals(4, count($p->getAdapter()));
    }

    /**
     * @group ZF-7602
     */
    public function testInvalidDataInConstructor_ThrowsException()
    {
        $this->setExpectedException('Zend\Paginator\Exception\ExceptionInterface');

        new Paginator\Paginator(array());
    }

    /**
     * @group ZF-9396
     */
    public function testArrayAccessInClassSerializableLimitIterator()
    {
        $iterator  = new \ArrayIterator(array('zf9396', 'foo', null));
        $paginator = new Paginator\Paginator(new Adapter\Iterator($iterator));

        $this->assertEquals('zf9396', $paginator->getItem(1));

        $items = $paginator->getAdapter()
                           ->getItems(0, 10);

        $this->assertEquals('foo', $items[1]);
        $this->assertEquals(0, $items->key());
        $this->assertFalse(isset($items[2]));
        $this->assertTrue(isset($items[1]));
        $this->assertFalse(isset($items[3]));
    }

    public function testSetGlobalConfigThrowsInvalidArgumentException()
    {
        $this->setExpectedException(
            'Zend\Paginator\Exception\InvalidArgumentException',
            'setGlobalConfig expects an array or Traversable'
        );

        $this->paginator->setGlobalConfig('not array');
    }

    public function testSetScrollingStylePluginManagerWithStringThrowsInvalidArgumentException()
    {
        $this->setExpectedException(
            'Zend\Paginator\Exception\InvalidArgumentException',
            'Unable to locate scrolling style plugin manager with class "invalid adapter"; class not found'
        );

        $this->paginator->setScrollingStylePluginManager('invalid adapter');
    }

    public function testSetScrollingStylePluginManagerWithAdapterThrowsInvalidArgumentException()
    {
        $this->setExpectedException(
            'Zend\Paginator\Exception\InvalidArgumentException',
            'Pagination scrolling-style manager must extend ScrollingStylePluginManager; received "stdClass"'
        );

        $this->paginator->setScrollingStylePluginManager(
            new stdClass()
        );
    }

    public function testLoadScrollingStyleWithDigitThrowsInvalidArgumentException()
    {
        $adapter = new TestAsset\TestAdapter;
        $paginator = new Paginator\Paginator($adapter);
        $reflection = new ReflectionMethod($paginator, '_loadScrollingStyle');
        $reflection->setAccessible(true);

        $this->setExpectedException(
            'Zend\Paginator\Exception\InvalidArgumentException',
            'Scrolling style must be a class ' .
                'name or object implementing Zend\Paginator\ScrollingStyle\ScrollingStyleInterface'
        );

        $reflection->invoke($paginator, 12345);
    }

    public function testLoadScrollingStyleWithObjectThrowsInvalidArgumentException()
    {
        $adapter = new TestAsset\TestAdapter;
        $paginator = new Paginator\Paginator($adapter);
        $reflection = new ReflectionMethod($paginator, '_loadScrollingStyle');
        $reflection->setAccessible(true);

        $this->setExpectedException(
            'Zend\Paginator\Exception\InvalidArgumentException',
            'Scrolling style must implement Zend\Paginator\ScrollingStyle\ScrollingStyleInterface'
        );

        $reflection->invoke($paginator, new stdClass());
    }

    public function testGetCacheId()
    {
        $adapter = new TestAsset\TestAdapter;
        $paginator = new Paginator\Paginator($adapter);
        $reflectionGetCacheId = new ReflectionMethod($paginator, '_getCacheId');
        $reflectionGetCacheId->setAccessible(true);
        $outputGetCacheId = $reflectionGetCacheId->invoke($paginator, null);

        $reflectionGetCacheInternalId = new ReflectionMethod($paginator, '_getCacheInternalId');
        $reflectionGetCacheInternalId->setAccessible(true);
        $outputGetCacheInternalId = $reflectionGetCacheInternalId->invoke($paginator);

        $this->assertEquals($outputGetCacheId, 'Zend_Paginator_1_' . $outputGetCacheInternalId);
    }

}
