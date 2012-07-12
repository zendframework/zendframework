<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Spreadsheets;

use Zend\GData\Spreadsheets\ListFeed;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class ListFeedTest extends \PHPUnit_Framework_TestCase
{

    /** @var ListFeed */
    public $listFeed;

    public function setUp()
    {
        $this->listFeed = new ListFeed(
                file_get_contents(__DIR__ . '/_files/TestDataListFeedSample1.xml'),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertEquals(2, count($this->listFeed->entries));
        $this->assertEquals(2, $this->listFeed->entries->count());
        foreach($this->listFeed->entries as $entry) {
            $this->assertInstanceOf('Zend\GData\Spreadsheets\ListEntry', $entry);
        }

        $newListFeed = new ListFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->listFeed->saveXML());
        $newListFeed->transferFromDom($doc->documentElement);

        $this->assertEquals(2, count($newListFeed->entries));
        $this->assertEquals(2, $newListFeed->entries->count());
        foreach($newListFeed->entries as $entry) {
            $this->assertInstanceOf('Zend\GData\Spreadsheets\ListEntry', $entry);
        }

    }

}
