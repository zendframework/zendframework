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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Search_Lucene_AllTests::main');
}

require_once 'Zend/Search/Lucene/LuceneTest.php';

require_once 'Zend/Search/Lucene/DocumentTest.php';
require_once 'Zend/Search/Lucene/FSMTest.php';
require_once 'Zend/Search/Lucene/FieldTest.php';
require_once 'Zend/Search/Lucene/PriorityQueueTest.php';

require_once 'Zend/Search/Lucene/AnalysisTest.php';

require_once 'Zend/Search/Lucene/Index/DictionaryLoaderTest.php';
require_once 'Zend/Search/Lucene/Index/FieldInfoTest.php';
require_once 'Zend/Search/Lucene/Index/TermsPriorityQueueTest.php';
require_once 'Zend/Search/Lucene/Index/SegmentInfoTest.php';
require_once 'Zend/Search/Lucene/Index/SegmentMergerTest.php';
require_once 'Zend/Search/Lucene/Index/TermInfoTest.php';
require_once 'Zend/Search/Lucene/Index/TermTest.php';

require_once 'Zend/Search/Lucene/Storage/DirectoryTest.php';
require_once 'Zend/Search/Lucene/Storage/FileTest.php';

require_once 'Zend/Search/Lucene/SearchHighlightTest.php';

require_once 'Zend/Search/Lucene/SearchTest.php';
require_once 'Zend/Search/Lucene/Search23Test.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Search_Lucene');

        $suite->addTestSuite('Zend_Search_Lucene_LuceneTest');

        $suite->addTestSuite('Zend_Search_Lucene_DocumentTest');
        $suite->addTestSuite('Zend_Search_Lucene_FSMTest');
        $suite->addTestSuite('Zend_Search_Lucene_FieldTest');
        $suite->addTestSuite('Zend_Search_Lucene_PriorityQueueTest');

        $suite->addTestSuite('Zend_Search_Lucene_AnalysisTest');

        $suite->addTestSuite('Zend_Search_Lucene_Index_DictionaryLoaderTest');
        $suite->addTestSuite('Zend_Search_Lucene_Index_FieldInfoTest');
        $suite->addTestSuite('Zend_Search_Lucene_Index_TermsPriorityQueueTest');
        $suite->addTestSuite('Zend_Search_Lucene_Index_SegmentInfoTest');
        $suite->addTestSuite('Zend_Search_Lucene_Index_SegmentMergerTest');
        $suite->addTestSuite('Zend_Search_Lucene_Index_TermInfoTest');
        $suite->addTestSuite('Zend_Search_Lucene_Index_TermTest');
        /**
         * SegmentWriter class, its subclasses and Writer class are completely tested within
         * Lucene::addDocument and Lucene::optimize testing
         */

        $suite->addTestSuite('Zend_Search_Lucene_Storage_DirectoryTest');
        $suite->addTestSuite('Zend_Search_Lucene_Storage_FileTest');

        $suite->addTestSuite('Zend_Search_Lucene_SearchHighlightTest');

        $suite->addTestSuite('Zend_Search_Lucene_SearchTest');
        $suite->addTestSuite('Zend_Search_Lucene_Search23Test');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Search_Lucene_AllTests::main') {
    Zend_Search_Lucene_AllTests::main();
}
