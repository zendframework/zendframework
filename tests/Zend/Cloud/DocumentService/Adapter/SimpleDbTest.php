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
 * @package    Zend\Cloud\DocumentService\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\DocumentService\Adapter;

use ZendTest\Cloud\DocumentService\TestCase,
    Zend\Cloud\DocumentService\Adapter\SimpleDb as AdapterSimpleDb,
    Zend\Cloud\DocumentService\Document,
    Zend\Cloud\DocumentService\Factory,
    Zend\Config;

// Call ZendTest\Cloud\DocumentService\Adapter\SimpleDbTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ZendTest\Cloud\DocumentService\Adapter\SimpleDbTest::main");
}

/**
 * @category   Zend
 * @package    Zend\Cloud\DocumentService\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SimpleDbTest extends TestCase
{
    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 10;

    protected $_clientType = 'Zend\Service\Amazon\SimpleDb';

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testUpdateDocumentMergeAll()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testMerge");
        $this->_commonDocument->createCollection($name);

        $doc = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc);
        $doc1 = $this->_makeDocument($data[1]);
        $this->_wait();
        $this->_commonDocument->updateDocument($name, $doc->getID(), $doc1,
            array(AdapterSimpleDb::MERGE_OPTION => true));
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc->getID());
        $this->assertTrue($fetchdoc instanceof Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertContains($doc->name, $fetchdoc->name, "Name field did not update: " . var_export($fetchdoc->getFields(), 1));
        $this->assertContains($doc1->name, $fetchdoc->name, "Name field did not update: " . var_export($fetchdoc->getFields(), 1));
        $this->assertContains((string) $doc->year, $fetchdoc->year, "Year field did not update: " . var_export($fetchdoc->getFields(), 1));
        $this->assertContains((string) $doc1->year, $fetchdoc->year, "Year field did not update: " . var_export($fetchdoc->getFields(), 1));

        $this->_commonDocument->deleteCollection($name);
    }

    public function testUpdateDocumentMergeSome()
    {
        $data = $this->_getDocumentData();
        $name = $this->_collectionName("testMerge");
        $this->_commonDocument->createCollection($name);

        $doc = $this->_makeDocument($data[0]);
        $this->_commonDocument->insertDocument($name, $doc);
        $doc1 = $this->_makeDocument($data[1]);
        $this->_wait();
        $this->_commonDocument->updateDocument($name, $doc->getID(), $doc1,
            array(AdapterSimpleDb::MERGE_OPTION =>
                array("year" => true, "pages" => true)));
        $this->_wait();

        $fetchdoc = $this->_commonDocument->fetchDocument($name, $doc->getID());
        $this->assertTrue($fetchdoc instanceof Zend\Cloud\DocumentService\Document, "New document not found");
        $this->assertEquals($doc1->name, $fetchdoc->name, "Name field did not update");
        $this->assertContains((string) $doc1->pages, $fetchdoc->pages, "Page field did not update");
        $this->assertContains((string) $doc->pages, $fetchdoc->pages, "Page field did not update");
        $this->assertContains((string) $doc1->year, $fetchdoc->year, "Year field did not update");
        $this->assertContains((string) $doc->year, $fetchdoc->year, "Year field did not update");

        $this->_commonDocument->deleteCollection($name);
    }

    static function getConfigArray()
    {
        return array(
                Factory::DOCUMENT_ADAPTER_KEY => '\Zend\Cloud\DocumentService\Adapter\SimpleDb',
                AdapterSimpleDb::AWS_ACCESS_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
                AdapterSimpleDb::AWS_SECRET_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY'),
            );
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED') ||
            !constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED') ||
            !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID') ||
            !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')) {
            $this->markTestSkipped("Amazon SimpleDB access not configured, skipping test");
        }

        $config = new Config(self::getConfigArray());

        return $config;
    }

    protected function _getDocumentData()
    {
        return array(
            array(
	        	parent::ID_FIELD => "0385333498",
	        	"name" =>	"The Sirens of Titan",
	        	"author" =>	"Kurt Vonnegut",
	        	"year"	=> 1959,
	        	"pages" =>	336,
	        	"keyword" => array("Book", "Paperback")
	        	),
            array(
	        	parent::ID_FIELD => "0802131786",
	        	"name" =>	"Tropic of Cancer",
	        	"author" =>	"Henry Miller",
	        	"year"	=> 1934,
	        	"pages" =>	318,
	        	"keyword" => array("Book")
	        	),
            array(
	        	parent::ID_FIELD => "B000T9886K",
	        	"name" =>	"In Between",
	        	"author" =>	"Paul Van Dyk",
	        	"year"	=> 2007,
	        	"keyword" => array("CD", "Music")
	        	),
	        array(
	        	parent::ID_FIELD => "1579124585",
	        	"name" =>	"The Right Stuff",
	        	"author" =>	"Tom Wolfe",
	        	"year"	=> 1979,
	        	"pages" =>	304,
	        	"keyword" => array("American", "Book", "Hardcover")
	        	),
        );
    }

    protected function _queryString($domain, $s1, $s2)
    {
        return "select * from $domain where itemName() = '$s1' OR itemName() = '$s2'";
    }

}

if (PHPUnit_MAIN_METHOD == 'ZendTest\Cloud\DocumentService\Adapter\SimpleDbTest::main') {
    ZendTest\Cloud\DocumentService\Adapter\SimpleDbTest::main();
}