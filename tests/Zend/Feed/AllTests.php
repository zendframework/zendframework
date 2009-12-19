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

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Feed_AllTests::main');
}

require_once 'Zend/Feed/ArrayAccessTest.php';
require_once 'Zend/Feed/AtomEntryOnlyTest.php';
require_once 'Zend/Feed/AtomPublishingTest.php';
require_once 'Zend/Feed/CountTest.php';
require_once 'Zend/Feed/ElementTest.php';
require_once 'Zend/Feed/ImportTest.php';
require_once 'Zend/Feed/IteratorTest.php';
require_once 'Zend/Feed/Entry/RssTest.php';

require_once 'Zend/Feed/ReaderTest.php';
require_once 'Zend/Feed/Reader/Feed/RssTest.php';
require_once 'Zend/Feed/Reader/Entry/RssTest.php';
require_once 'Zend/Feed/Reader/Feed/AtomTest.php';
require_once 'Zend/Feed/Reader/Entry/AtomTest.php';
require_once 'Zend/Feed/Reader/Feed/CommonTest.php';
require_once 'Zend/Feed/Reader/Entry/CommonTest.php';

require_once 'Zend/Feed/Reader/Feed/AtomSourceTest.php';
require_once 'Zend/Feed/Reader/Entry/AtomStandaloneEntryTest.php';

require_once 'Zend/Feed/Reader/Integration/WordpressRss2DcAtomTest.php';
require_once 'Zend/Feed/Reader/Integration/WordpressAtom10Test.php';
require_once 'Zend/Feed/Reader/Integration/LautDeRdfTest.php';
require_once 'Zend/Feed/Reader/Integration/H-OnlineComAtom10Test.php';

require_once 'Zend/Feed/Writer/FeedTest.php';
require_once 'Zend/Feed/Writer/EntryTest.php';
require_once 'Zend/Feed/Writer/Renderer/Feed/AtomTest.php';
require_once 'Zend/Feed/Writer/Renderer/Feed/RssTest.php';
require_once 'Zend/Feed/Writer/Renderer/Entry/AtomTest.php';
require_once 'Zend/Feed/Writer/Renderer/Entry/RssTest.php';

require_once 'Zend/Feed/Writer/Extension/ITunes/EntryTest.php';
require_once 'Zend/Feed/Writer/Extension/ITunes/FeedTest.php';

require_once 'Zend/Feed/Pubsubhubbub/AllTests.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Feed_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Feed');

        $suite->addTestSuite('Zend_Feed_ArrayAccessTest');
        $suite->addTestSuite('Zend_Feed_AtomEntryOnlyTest');
        $suite->addTestSuite('Zend_Feed_AtomPublishingTest');
        $suite->addTestSuite('Zend_Feed_CountTest');
        $suite->addTestSuite('Zend_Feed_ElementTest');
        $suite->addTestSuite('Zend_Feed_ImportTest');
        $suite->addTestSuite('Zend_Feed_IteratorTest');
        $suite->addTestSuite('Zend_Feed_Entry_RssTest');

        /* Zend_Feed_Reader tests */
        // Base parent class
        $suite->addTestSuite('Zend_Feed_ReaderTest');
        // RSS - Feed Level
        $suite->addTestSuite('Zend_Feed_Reader_Feed_RssTest');
        // RSS - Item Level
        $suite->addTestSuite('Zend_Feed_Reader_Entry_RssTest');
        // ATOM - Feed Level
        $suite->addTestSuite('Zend_Feed_Reader_Feed_AtomTest');
        // ATOM - Item Level
        $suite->addTestSuite('Zend_Feed_Reader_Entry_AtomTest');
        // COMMON - Feed Level
        $suite->addTestSuite('Zend_Feed_Reader_Feed_CommonTest');
        // COMMON - Entry Level
        $suite->addTestSuite('Zend_Feed_Reader_Entry_CommonTest');
        // ATOM - Entry Level (Source Feed Metadata)
        $suite->addTestSuite('Zend_Feed_Reader_Feed_AtomSourceTest');
        // ATOM - Entry Level (Standalone Entry Documents)
        $suite->addTestSuite('Zend_Feed_Reader_Entry_AtomStandaloneEntryTest');
        /**
         * Real World Feed Tests
         */
        $suite->addTestSuite('Zend_Feed_Reader_Integration_WordpressRss2DcAtomTest');
        $suite->addTestSuite('Zend_Feed_Reader_Integration_WordpressAtom10Test');
        $suite->addTestSuite('Zend_Feed_Reader_Integration_LautDeRdfTest');
        $suite->addTestSuite('Zend_Feed_Reader_Integration_HOnlineComAtom10Test');
        
        $suite->addTestSuite('Zend_Feed_Writer_FeedTest');
        $suite->addTestSuite('Zend_Feed_Writer_EntryTest');
        $suite->addTestSuite('Zend_Feed_Writer_Renderer_Feed_AtomTest');
        $suite->addTestSuite('Zend_Feed_Writer_Renderer_Feed_RssTest');
        $suite->addTestSuite('Zend_Feed_Writer_Renderer_Entry_AtomTest');
        $suite->addTestSuite('Zend_Feed_Writer_Renderer_Entry_RssTest');
        
        $suite->addTestSuite('Zend_Feed_Writer_Extension_ITunes_EntryTest');
        $suite->addTestSuite('Zend_Feed_Writer_Extension_ITunes_FeedTest');
        
        $suite->addTest(Zend_Feed_Pubsubhubbub_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Feed_AllTests::main') {
    Zend_Feed_AllTests::main();
}
