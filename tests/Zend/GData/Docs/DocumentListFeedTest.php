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

namespace ZendTest\GData\Docs;

use DOMDocument;
use Zend\GData\Docs\DocumentListFeed;

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

    /** @var DocumentListFeed */
    public $docFeed;

    public function setUp()
    {
        $this->docFeed = new DocumentListFeed(
                file_get_contents(__DIR__ . '/_files/TestDataDocumentListFeedSample.xml'),
                true);
    }

    public function testToAndFromString()
    {
        // There should be 2 entries in the feed.
        $this->assertEquals(2, count($this->docFeed->entries));
        $this->assertEquals(2, $this->docFeed->entries->count());
        foreach($this->docFeed->entries as $entry)
        {
            $this->assertInstanceOf('Zend\GData\Docs\DocumentListEntry', $entry);
        }

        $newDocFeed = new DocumentListFeed();
        $doc = new DOMDocument();
        $doc->loadXML($this->docFeed->saveXML());
        $newDocFeed->transferFromDom($doc->documentElement);

        $this->assertEquals(count($newDocFeed->entries), count($this->docFeed->entries));
        foreach($newDocFeed->entries as $entry)
        {
            $this->assertInstanceOf('Zend\GData\Docs\DocumentListEntry', $entry);
        }
    }

}
