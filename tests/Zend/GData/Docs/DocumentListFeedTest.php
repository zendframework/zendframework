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
 * @package    Zend_GData_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Docs;
use Zend\GData\Docs;

/**
 * @category   Zend
 * @package    Zend_GData_Docs
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Docs
 */
class DocumentListFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->docFeed = new Docs\DocumentListFeed(
                file_get_contents(__DIR__ . '/_files/TestDataDocumentListFeedSample.xml'),
                true);
    }

    public function testToAndFromString()
    {
        // There should be 2 entries in the feed.
        $this->assertTrue(count($this->docFeed->entries) == 1);
        foreach($this->docFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Docs\DocumentListEntry);
        }

        $newDocFeed = new Docs\DocumentListFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->docFeed->saveXML());
        $newDocFeed->transferFromDom($doc->documentElement);

        $this->assertTrue(count($newDocFeed->entries) == count($this->docFeed->entries));
        foreach($newDocFeed->entries as $entry)
        {
            $this->assertTrue($entry instanceof Docs\DocumentListEntry);
        }
    }

}
