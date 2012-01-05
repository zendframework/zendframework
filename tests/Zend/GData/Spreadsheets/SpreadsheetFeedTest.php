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
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Spreadsheets;
use Zend\GData\Spreadsheets;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class SpreadsheetFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->sprFeed = new Spreadsheets\SpreadsheetFeed(
                file_get_contents(__DIR__ . '/_files/TestDataSpreadsheetFeedSample1.xml'),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertTrue(count($this->sprFeed->entries) == 1);
        foreach($this->sprFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Spreadsheets\SpreadsheetEntry);
        }

        $newSprFeed = new Spreadsheets\SpreadsheetFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->sprFeed->saveXML());
        $newSprFeed->transferFromDom($doc->documentElement);

        $this->assertTrue(count($newSprFeed->entries) == 1);
        foreach($newSprFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Spreadsheets\SpreadsheetEntry);
        }
    }

}
