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
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_PaginatorTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_PaginatorTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * @see Zend_Paginator
 */
require_once 'Zend/Paginator.php';

/**
 * @see Zend_Paginator_AdapterAggregate
 */
require_once 'Zend/Paginator/AdapterAggregate.php';

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Config_Xml
 */
require_once 'Zend/Config/Xml.php';

/**
 * @see Zend_Db_Adapter_Pdo_Sqlite
 */
require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';

/**
 * @see Zend_View
 */
require_once 'Zend/View.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_View_Helper_PaginationControl
 */
require_once 'Zend/View/Helper/PaginationControl.php';

/**
 * @see Zf4207
 */
require_once 'Zend/Paginator/_files/Zf4207.php';

/**
 * @see TestTable
 */
require_once 'Zend/Paginator/_files/TestTable.php';

/**
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Zend_Filter_Callback
 */
require_once 'Zend/Filter/Callback.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Paginator
 */
class Zend_PaginatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Paginator instance
     *
     * @var Zend_Paginator
     */
    protected $_paginator = null;

    protected $_testCollection = null;

    protected $_cache;

    protected $_query = null;

    protected $_config = null;

    protected $_adapter = null;

    protected function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
           $this->markTestSkipped('Pdo_Sqlite extension is not loaded');
        }

        $this->_adapter = new Zend_Db_Adapter_Pdo_Sqlite(array(
            'dbname' => dirname(__FILE__) . '/Paginator/_files/test.sqlite'
        ));

        $this->_query = $this->_adapter->select()->from('test');

        $this->_testCollection = range(1, 101);
        $this->_paginator = Zend_Paginator::factory($this->_testCollection);

        $this->_config = new Zend_Config_Xml(dirname(__FILE__) . '/Paginator/_files/config.xml');
        // get a fresh new copy of ViewRenderer in each tests
        Zend_Controller_Action_HelperBroker::resetHelpers();

        $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
        $bO = array('cache_dir'=> $this->_getTmpDir());

        $this->_cache = Zend_Cache::factory('Core', 'File', $fO, $bO);

        Zend_Paginator::setCache($this->_cache);

        $this->_restorePaginatorDefaults();
    }

    protected function tearDown()
    {
        $this->_dbConn = null;
        $this->_testCollection = null;
        $this->_paginator = null;
    }

    protected function _getTmpDir()
    {
        $tmpDir = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'zend_paginator';
        if (file_exists($tmpDir)) {
            $this->_rmDirRecursive($tmpDir);
        }
        mkdir($tmpDir);
        $this->cacheDir = $tmpDir;
        return $tmpDir;
    }

    protected function _rmDirRecursive($path)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $file) {
            if (!$file->isDir()) {
                unlink($file->getPathname());
            } elseif (!in_array($file->getFilename(), array('.', '..'))) {
                $this->_rmDirRecursive($file->getPathname());
            }
        }
        unset($file, $dir); // required on windows to remove file handle
        if (!rmdir($path)) {
            throw new Exception('Unable to remove temporary directory ' . $path
                                . '; perhaps it has a nested structure?');
        }
    }

    protected function _restorePaginatorDefaults()
    {
        $this->_paginator->setItemCountPerPage(10);
        $this->_paginator->setCurrentPageNumber(1);
        $this->_paginator->setPageRange(10);
        $this->_paginator->setView();

        Zend_Paginator::setDefaultScrollingStyle();
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(null);

        Zend_Paginator::setConfig($this->_config->default);

        $loader = Zend_Paginator::getScrollingStyleLoader();
        $loader->clearPaths();
        $loader->addPrefixPath('Zend_Paginator_ScrollingStyle', 'Zend/Paginator/ScrollingStyle');

        $this->_cache->clean();
        $this->_paginator->setCacheEnabled(true);
    }

    public function testFactoryReturnsArrayAdapter()
    {
        $paginator = Zend_Paginator::factory($this->_testCollection);
        $this->assertType('Zend_Paginator_Adapter_Array', $paginator->getAdapter());
    }

    public function testFactoryReturnsDbSelectAdapter()
    {
        $paginator = Zend_Paginator::factory($this->_query);

        $this->assertType('Zend_Paginator_Adapter_DbSelect', $paginator->getAdapter());
    }

    // ZF-4607
    public function testFactoryReturnsDbTableSelectAdapter()
    {
        $table = new TestTable($this->_adapter);

        $paginator = Zend_Paginator::factory($table->select());

        $this->assertType('Zend_Paginator_Adapter_DbSelect', $paginator->getAdapter());
    }

    public function testFactoryReturnsIteratorAdapter()
    {
        $paginator = Zend_Paginator::factory(new ArrayIterator($this->_testCollection));
        $this->assertType('Zend_Paginator_Adapter_Iterator', $paginator->getAdapter());
    }

    public function testFactoryReturnsNullAdapter()
    {
        $paginator = Zend_Paginator::factory(101);
        $this->assertType('Zend_Paginator_Adapter_Null', $paginator->getAdapter());
    }

    public function testFactoryThrowsInvalidClassExceptionAdapter()
    {
        try {
            $paginator = Zend_Paginator::factory(new stdClass());
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('stdClass', $e->getMessage());
        }
    }

    public function testFactoryThrowsInvalidTypeExceptionAdapter()
    {
        try {
            $paginator = Zend_Paginator::factory('invalid argument');
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('string', $e->getMessage());
        }
    }

    public function testAddsSingleScrollingStylePrefixPath()
    {
        Zend_Paginator::addScrollingStylePrefixPath('prefix1', 'path1');
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();

        $this->assertArrayHasKey('prefix1_', $paths);
        $this->assertEquals($paths['prefix1_'], array('path1/'));

        $loader->clearPaths('prefix1');
    }

    public function testAddsSingleScrollingStylePrefixPathWithArray()
    {
        Zend_Paginator::addScrollingStylePrefixPaths(array('prefix' => 'prefix2',
                                                           'path'   => 'path2'));
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();

        $this->assertArrayHasKey('prefix2_', $paths);
        $this->assertEquals($paths['prefix2_'], array('path2/'));

        $loader->clearPaths('prefix2');
    }

    public function testAddsMultipleScrollingStylePrefixPaths()
    {
        $paths = array('prefix3' => 'path3',
                       'prefix4' => 'path4',
                       'prefix5' => 'path5');

        Zend_Paginator::addScrollingStylePrefixPaths($paths);
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();

        for ($i = 3; $i <= 5; $i++) {
            $prefix = 'prefix' . $i . '_';
            $this->assertArrayHasKey($prefix, $paths);
            $this->assertEquals($paths[$prefix], array('path' . $i . '/'));
        }

        $loader->clearPaths('prefix3');
        $loader->clearPaths('prefix4');
        $loader->clearPaths('prefix5');
    }

    public function testAddsSingleAdapterPrefixPath()
    {
        Zend_Paginator::addAdapterPrefixPath('prefix1', 'path1');
        $loader = Zend_Paginator::getAdapterLoader();
        $paths = $loader->getPaths();

        $this->assertArrayHasKey('prefix1_', $paths);
        $this->assertEquals($paths['prefix1_'], array('path1/'));

        $loader->clearPaths('prefix1');
    }

    public function testAddsSingleAdapterPrefixPathWithArray()
    {
        Zend_Paginator::addAdapterPrefixPaths(array('prefix' => 'prefix2',
                                                    'path'   => 'path2'));
        $loader = Zend_Paginator::getAdapterLoader();
        $paths = $loader->getPaths();

        $this->assertArrayHasKey('prefix2_', $paths);
        $this->assertEquals($paths['prefix2_'], array('path2/'));

        $loader->clearPaths('prefix2');
    }

    public function testAddsMultipleAdapterPrefixPaths()
    {
        $paths = array('prefix3' => 'path3',
                       'prefix4' => 'path4',
                       'prefix5' => 'path5');

        Zend_Paginator::addAdapterPrefixPaths($paths);
        $loader = Zend_Paginator::getAdapterLoader();
        $paths = $loader->getPaths();

        for ($i = 3; $i <= 5; $i++) {
            $prefix = 'prefix' . $i . '_';
            $this->assertArrayHasKey($prefix, $paths);
            $this->assertEquals($paths[$prefix], array('path' . $i . '/'));
        }

        $loader->clearPaths('prefix3');
        $loader->clearPaths('prefix4');
        $loader->clearPaths('prefix5');
    }

    public function testGetsAndSetsDefaultScrollingStyle()
    {
        $this->assertEquals(Zend_Paginator::getDefaultScrollingStyle(), 'Sliding');
        Zend_Paginator::setDefaultScrollingStyle('Scrolling');
        $this->assertEquals(Zend_Paginator::getDefaultScrollingStyle(), 'Scrolling');
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
    }

    public function testHasCorrectCountAfterInit()
    {
        $paginator = Zend_Paginator::factory(range(1, 101));
        $this->assertEquals(11, $paginator->count());
    }

    public function testHasCorrectCountOfAllItemsAfterInit()
    {
        $paginator = Zend_Paginator::factory(range(1, 101));
        $this->assertEquals(101, $paginator->getTotalItemCount());
    }

    public function testAddCustomAdapterPathsInConstructor()
    {
        $paginator = Zend_Paginator::factory(range(1, 101), Zend_Paginator::INTERNAL_ADAPTER, array('My_Paginator_Adapter' => 'My/Paginator/Adapter'));

        $loader = Zend_Paginator::getAdapterLoader();
        $paths = $loader->getPaths();

        $this->assertEquals(2, count($paths));
        $this->assertEquals(array('Zend_Paginator_Adapter_' => array('Zend/Paginator/Adapter/'),
                                  'My_Paginator_Adapter_' => array('My/Paginator/Adapter/')), $paths);

        $loader->clearPaths('My_Paginator_Adapter');
    }

    public function testLoadsFromConfig()
    {
        Zend_Paginator::setConfig($this->_config->testing);
        $this->assertEquals('Scrolling', Zend_Paginator::getDefaultScrollingStyle());

        $paths = array(
            'prefix6' => 'path6',
            'prefix7' => 'path7',
            'prefix8' => 'path8'
        );

        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();

        for ($i = 6; $i <= 8; $i++) {
            $prefix = 'prefix' . $i . '_';
            $this->assertArrayHasKey($prefix, $paths);
            $this->assertEquals($paths[$prefix], array('path' . $i . '/'));
        }

        $loader->clearPaths('prefix6');
        $loader->clearPaths('prefix7');
        $loader->clearPaths('prefix8');

        $loader = Zend_Paginator::getAdapterLoader();
        $paths = $loader->getPaths();

        for ($i = 6; $i <= 8; $i++) {
            $prefix = 'prefix' . $i . '_';
            $this->assertArrayHasKey($prefix, $paths);
            $this->assertEquals($paths[$prefix], array('path' . $i . '/'));
        }

        $loader->clearPaths('prefix6');
        $loader->clearPaths('prefix7');
        $loader->clearPaths('prefix8');

        $paginator = Zend_Paginator::factory(range(1, 101));
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

        $actual = $this->_paginator->getPages();

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

        $this->_paginator->setCurrentPageNumber(2);
        $actual = $this->_paginator->getPages();

        $this->assertEquals($expected, $actual);
    }

    public function testRendersWithoutPartial()
    {
        $this->_paginator->setView(new Zend_View());
        $string = @$this->_paginator->__toString();
        $this->assertEquals('', $string);
    }

    public function testRendersWithPartial()
    {
        $view = new Zend_View();
        $view->addBasePath(dirname(__FILE__) . '/Paginator/_files');
        $view->addHelperPath(dirname(__FILE__) . '/../../../trunk/library/Zend/View/Helper', 'Zend_View_Helper');

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partial.phtml');

        $this->_paginator->setView($view);

        $string = $this->_paginator->__toString();
        $this->assertEquals('partial rendered successfully', $string);
    }

    public function testGetsPageCount()
    {
        $this->assertEquals(11, $this->_paginator->count());
    }

    public function testGetsAndSetsItemCountPerPage()
    {
        Zend_Paginator::setConfig(new Zend_Config(array()));
        $this->_paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array(range(1, 101)));
        $this->assertEquals(10, $this->_paginator->getItemCountPerPage());
        $this->_paginator->setItemCountPerPage(15);
        $this->assertEquals(15, $this->_paginator->getItemCountPerPage());
        $this->_paginator->setItemCountPerPage(0);
        $this->assertEquals(10, $this->_paginator->getItemCountPerPage());
        $this->_paginator->setItemCountPerPage(10);
    }

    public function testGetsCurrentItemCount()
    {
        $this->_paginator->setItemCountPerPage(10);
        $this->_paginator->setPageRange(10);

        $this->assertEquals(10, $this->_paginator->getCurrentItemCount());

        $this->_paginator->setCurrentPageNumber(11);

        $this->assertEquals(1, $this->_paginator->getCurrentItemCount());

        $this->_paginator->setCurrentPageNumber(1);
    }

    public function testGetsCurrentItems()
    {
        $items = $this->_paginator->getCurrentItems();
        $this->assertType('ArrayIterator', $items);

        $count = 0;

        foreach ($items as $item) {
            $count++;
        }

        $this->assertEquals(10, $count);
    }

    public function testGetsIterator()
    {
        $items = $this->_paginator->getIterator();
        $this->assertType('ArrayIterator', $items);

        $count = 0;

        foreach ($items as $item) {
            $count++;
        }

        $this->assertEquals(10, $count);
    }

    public function testGetsAndSetsCurrentPageNumber()
    {
        $this->assertEquals(1, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(-1);
        $this->assertEquals(1, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(11);
        $this->assertEquals(11, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(111);
        $this->assertEquals(11, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(1);
        $this->assertEquals(1, $this->_paginator->getCurrentPageNumber());
    }

    public function testGetsAbsoluteItemNumber()
    {
        $this->assertEquals(1, $this->_paginator->getAbsoluteItemNumber(1));
        $this->assertEquals(11, $this->_paginator->getAbsoluteItemNumber(1, 2));
        $this->assertEquals(24, $this->_paginator->getAbsoluteItemNumber(4, 3));
    }

    public function testGetsItem()
    {
        $this->assertEquals(1, $this->_paginator->getItem(1));
        $this->assertEquals(11, $this->_paginator->getItem(1, 2));
        $this->assertEquals(24, $this->_paginator->getItem(4, 3));
    }

    public function testThrowsExceptionWhenCollectionIsEmpty()
    {
        $paginator = Zend_Paginator::factory(array());

        try {
            $paginator->getItem(1);
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('Page 1 does not exist', $e->getMessage());
        }
    }

    public function testThrowsExceptionWhenRetrievingNonexistentItemFromLastPage()
    {
        try {
            $this->_paginator->getItem(10, 11);
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('Page 11 does not contain item number 10', $e->getMessage());
        }
    }

    public function testNormalizesPageNumber()
    {
        $this->assertEquals(1, $this->_paginator->normalizePageNumber(0));
        $this->assertEquals(1, $this->_paginator->normalizePageNumber(1));
        $this->assertEquals(2, $this->_paginator->normalizePageNumber(2));
        $this->assertEquals(5, $this->_paginator->normalizePageNumber(5));
        $this->assertEquals(10, $this->_paginator->normalizePageNumber(10));
        $this->assertEquals(11, $this->_paginator->normalizePageNumber(11));
        $this->assertEquals(11, $this->_paginator->normalizePageNumber(12));
    }

    public function testNormalizesItemNumber()
    {
        $this->assertEquals(1, $this->_paginator->normalizeItemNumber(0));
        $this->assertEquals(1, $this->_paginator->normalizeItemNumber(1));
        $this->assertEquals(2, $this->_paginator->normalizeItemNumber(2));
        $this->assertEquals(5, $this->_paginator->normalizeItemNumber(5));
        $this->assertEquals(9, $this->_paginator->normalizeItemNumber(9));
        $this->assertEquals(10, $this->_paginator->normalizeItemNumber(10));
        $this->assertEquals(10, $this->_paginator->normalizeItemNumber(11));
    }

    /**
     * @group ZF-8656
     */
    public function testNormalizesPageNumberWhenGivenAFloat()
    {
        $this->assertEquals(1, $this->_paginator->normalizePageNumber(0.5));
        $this->assertEquals(1, $this->_paginator->normalizePageNumber(1.99));
        $this->assertEquals(2, $this->_paginator->normalizePageNumber(2.3));
        $this->assertEquals(5, $this->_paginator->normalizePageNumber(5.1));
        $this->assertEquals(10, $this->_paginator->normalizePageNumber(10.06));
        $this->assertEquals(11, $this->_paginator->normalizePageNumber(11.5));
        $this->assertEquals(11, $this->_paginator->normalizePageNumber(12.7889));
    }

    /**
     * @group ZF-8656
     */
    public function testNormalizesItemNumberWhenGivenAFloat()
    {
        $this->assertEquals(1, $this->_paginator->normalizeItemNumber(0.5));
        $this->assertEquals(1, $this->_paginator->normalizeItemNumber(1.99));
        $this->assertEquals(2, $this->_paginator->normalizeItemNumber(2.3));
        $this->assertEquals(5, $this->_paginator->normalizeItemNumber(5.1));
        $this->assertEquals(9, $this->_paginator->normalizeItemNumber(9.06));
        $this->assertEquals(10, $this->_paginator->normalizeItemNumber(10.5));
        $this->assertEquals(10, $this->_paginator->normalizeItemNumber(11.7889));
    }

    public function testGetsPagesInSubsetRange()
    {
        $actual = $this->_paginator->getPagesInRange(3, 8);
        $this->assertEquals(array_combine(range(3, 8), range(3, 8)), $actual);
    }

    public function testGetsPagesInOutOfBoundsRange()
    {
        $actual = $this->_paginator->getPagesInRange(-1, 12);
        $this->assertEquals(array_combine(range(1, 11), range(1, 11)), $actual);
    }

    public function testGetsItemsByPage()
    {
        $expected = new ArrayIterator(range(1, 10));

        $page1 = $this->_paginator->getItemsByPage(1);

        $this->assertEquals($page1, $expected);
        $this->assertEquals($page1, $this->_paginator->getItemsByPage(1));
    }

    public function testGetsItemCount()
    {
        $this->assertEquals(101, $this->_paginator->getItemCount(range(1, 101)));

        $limitIterator = new LimitIterator(new ArrayIterator(range(1, 101)));
        $this->assertEquals(101, $this->_paginator->getItemCount($limitIterator));
    }

    public function testGetsViewFromViewRenderer()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setView(new Zend_View());

        $this->assertType('Zend_View_Interface', $this->_paginator->getView());
    }

    public function testGeneratesViewIfNonexistent()
    {
        $this->assertType('Zend_View_Interface', $this->_paginator->getView());
    }

    public function testGetsAndSetsView()
    {
        $this->_paginator->setView(new Zend_View());
        $this->assertType('Zend_View_Interface', $this->_paginator->getView());
    }

    public function testRenders()
    {
        try {
            $this->_paginator->render(new Zend_View());
        } catch (Exception $e) {
            $this->assertType('Zend_View_Exception', $e);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }

    public function testGetsAndSetsPageRange()
    {
        $this->assertEquals(10, $this->_paginator->getPageRange());
        $this->_paginator->setPageRange(15);
        $this->assertEquals(15, $this->_paginator->getPageRange());
    }

    /**
     * @group ZF-3720
     */
    public function testGivesCorrectItemCount()
    {
        $paginator = Zend_Paginator::factory(range(1, 101));
        $paginator->setCurrentPageNumber(5)
                  ->setItemCountPerPage(5);
        $expected = new ArrayIterator(range(21, 25));

        $this->assertEquals($expected, $paginator->getCurrentItems());
    }

    /**
     * @group ZF-3737
     */
    public function testKeepsCurrentPageNumberAfterItemCountPerPageSet()
    {
        $paginator = Zend_Paginator::factory(array('item1', 'item2'));
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
        $this->_paginator->setCurrentPageNumber(3.3);
        $this->assertTrue($this->_paginator->getCurrentPageNumber() == 3);

        // Item count per page
        $this->_paginator->setItemCountPerPage(3.3);
        $this->assertTrue($this->_paginator->getItemCountPerPage() == 3);

        // Page range
        $this->_paginator->setPageRange(3.3);
        $this->assertTrue($this->_paginator->getPageRange() == 3);
    }

    /**
     * @group ZF-4207
     */
    public function testAcceptsTraversableInstanceFromAdapter()
    {
        $paginator = new Zend_Paginator(new Zf4207());
        $this->assertType('ArrayObject', $paginator->getCurrentItems());
    }

    public function testCachedItem()
    {
        $this->_paginator->setCurrentPageNumber(1)->getCurrentItems();
        $this->_paginator->setCurrentPageNumber(2)->getCurrentItems();
        $this->_paginator->setCurrentPageNumber(3)->getCurrentItems();

        $pageItems = $this->_paginator->getPageItemCache();
        $expected = array(
           1 => new ArrayIterator(range(1, 10)),
           2 => new ArrayIterator(range(11, 20)),
           3 => new ArrayIterator(range(21, 30))
        );
        $this->assertEquals($expected, $pageItems);
    }

    public function testClearPageItemCache()
    {
        $this->_paginator->setCurrentPageNumber(1)->getCurrentItems();
        $this->_paginator->setCurrentPageNumber(2)->getCurrentItems();
        $this->_paginator->setCurrentPageNumber(3)->getCurrentItems();

        // clear only page 2 items
        $this->_paginator->clearPageItemCache(2);
        $pageItems = $this->_paginator->getPageItemCache();
        $expected = array(
           1 => new ArrayIterator(range(1, 10)),
           3 => new ArrayIterator(range(21, 30))
        );
        $this->assertEquals($expected, $pageItems);

        // clear all
        $this->_paginator->clearPageItemCache();
        $pageItems = $this->_paginator->getPageItemCache();
        $this->assertEquals(array(), $pageItems);
    }

    public function testWithCacheDisabled()
    {
        $this->_paginator->setCacheEnabled(false);
        $this->_paginator->setCurrentPageNumber(1)->getCurrentItems();

        $cachedPageItems = $this->_paginator->getPageItemCache();
        $expected = new ArrayIterator(range(1, 10));

        $this->assertEquals(array(), $cachedPageItems);

        $pageItems = $this->_paginator->getCurrentItems();

        $this->assertEquals($expected, $pageItems);
    }

    public function testCacheDoesNotDisturbResultsWhenChangingParam()
    {
        $this->_paginator->setCurrentPageNumber(1)->getCurrentItems();
        $pageItems = $this->_paginator->setItemCountPerPage(5)->getCurrentItems();

        $expected = new ArrayIterator(range(1, 5));
        $this->assertEquals($expected, $pageItems);

        $pageItems = $this->_paginator->getItemsByPage(2);
        $expected = new ArrayIterator(range(6, 10));
        $this->assertEquals($expected, $pageItems);

        // change the inside Paginator scale
        $pageItems = $this->_paginator->setItemCountPerPage(8)->setCurrentPageNumber(3)->getCurrentItems();

        $pageItems = $this->_paginator->getPageItemCache();
        $expected = array(3 => new ArrayIterator(range(17, 24)));
        $this->assertEquals($expected, $pageItems);

        // get back to already cached data
        $this->_paginator->setItemCountPerPage(5);
        $pageItems = $this->_paginator->getPageItemCache();
        $expected =array(1 => new ArrayIterator(range(1, 5)),
                         2 => new ArrayIterator(range(6, 10)));
        $this->assertEquals($expected, $pageItems);
    }

    public function testToJson()
    {
        $this->_paginator->setCurrentPageNumber(1);

        $json = $this->_paginator->toJson();

        $expected = '"0":1,"1":2,"2":3,"3":4,"4":5,"5":6,"6":7,"7":8,"8":9,"9":10';

        $this->assertContains($expected, $json);
    }

    // ZF-5519
    public function testFilter()
    {
        $filter = new Zend_Filter_Callback(array($this, 'filterCallback'));
        $paginator = Zend_Paginator::factory(range(1, 10));
        $paginator->setFilter($filter);

        $page = $paginator->getCurrentItems();

        $this->assertEquals(new ArrayIterator(range(10, 100, 10)), $page);
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
        Zend_Paginator::setConfig(new Zend_Config(array()));

        $paginator = Zend_Paginator::factory(range(1, 10));
        $this->assertEquals(10, $paginator->getItemCountPerPage());

        Zend_Paginator::setDefaultItemCountPerPage(20);
        $this->assertEquals(20, Zend_Paginator::getDefaultItemCountPerPage());

        $paginator = Zend_Paginator::factory(range(1, 10));
        $this->assertEquals(20, $paginator->getItemCountPerPage());

        $this->_restorePaginatorDefaults();
    }

    /**
     * @group ZF-7207
     */
    public function testItemCountPerPageByDefault()
    {
        $paginator = Zend_Paginator::factory(range(1,20));
        $this->assertEquals(2, $paginator->count());
    }

    /**
     * @group ZF-5427
     */
    public function testNegativeItemNumbers()
    {
        $this->assertEquals(10, $this->_paginator->getItem(-1, 1));
        $this->assertEquals(9, $this->_paginator->getItem(-2, 1));
        $this->assertEquals(101, $this->_paginator->getItem(-1, -1));
    }

    /**
     * @group ZF-7602
     */
    public function testAcceptAndHandlePaginatorAdapterAggregateDataInFactory()
    {
        $p = Zend_Paginator::factory(new Zend_Paginator_TestArrayAggregate());

        $this->assertEquals(1, count($p));
        $this->assertType('Zend_Paginator_Adapter_Array', $p->getAdapter());
        $this->assertEquals(4, count($p->getAdapter()));
    }

    /**
     * @group ZF-7602
     */
    public function testAcceptAndHandlePaginatorAdapterAggreageInConstructor()
    {
        $p = new Zend_Paginator(new Zend_Paginator_TestArrayAggregate());

        $this->assertEquals(1, count($p));
        $this->assertType('Zend_Paginator_Adapter_Array', $p->getAdapter());
        $this->assertEquals(4, count($p->getAdapter()));
    }

    /**
     * @group ZF-7602
     */
    public function testInvalidDataInConstructor_ThrowsException()
    {
        $this->setExpectedException("Zend_Paginator_Exception");

        $p = new Zend_Paginator(array());
    }
}

class Zend_Paginator_TestArrayAggregate implements Zend_Paginator_AdapterAggregate
{
    public function getPaginatorAdapter()
    {
        return new Zend_Paginator_Adapter_Array(array(1, 2, 3, 4));
    }
}

// Call Zend_PaginatorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD === 'Zend_PaginatorTest::main') {
    Zend_PaginatorTest::main();
}
