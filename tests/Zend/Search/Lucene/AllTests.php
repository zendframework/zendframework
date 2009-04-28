<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Search_Lucene_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

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
