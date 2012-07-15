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

use Zend\GData\Spreadsheets;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class WorksheetFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->wksFeed = new Spreadsheets\WorksheetFeed(
                file_get_contents(__DIR__ . '/_files/TestDataWorksheetFeedSample1.xml'),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertTrue(count($this->wksFeed->entries) == 1);
        foreach($this->wksFeed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\WorksheetEntry);
        }

        $newWksFeed = new Spreadsheets\WorksheetFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->wksFeed->saveXML());
        $newWksFeed->transferFromDom($doc->documentElement);

        $this->assertTrue(count($newWksFeed->entries) == 1);
        foreach($newWksFeed->entries as $entry) {
            $this->assertTrue($entry instanceof Spreadsheets\WorksheetEntry);
        }
    }

}
