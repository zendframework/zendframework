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
class ListFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->listFeed = new Spreadsheets\ListFeed(
                file_get_contents(__DIR__ . '/_files/TestDataListFeedSample1.xml'),
                true);
    }

    public function testToAndFromString()
    {
        $this->assertTrue(count($this->listFeed->entries) == 1);
        foreach($this->listFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Spreadsheets\ListEntry);
        }

        $newListFeed = new Spreadsheets\ListFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->listFeed->saveXML());
        $newListFeed->transferFromDom($doc->documentElement);

        $this->assertTrue(count($newListFeed->entries) == 1);
        foreach($newListFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Spreadsheets\ListEntry);
        }

    }

}
