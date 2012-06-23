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
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\GApps;

use Zend\GData\GApps\OwnerEntry;
use Zend\GData\GApps\OwnerFeed;

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class OwnerFeedTest extends \PHPUnit_Framework_TestCase
{
    protected $ownerFeed = null;

    /**
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $ownerFeedText = file_get_contents(
                'Zend/GData/GApps/_files/OwnerFeedDataSample1.xml',
                true);
        $this->ownerFeed = new OwnerFeed($ownerFeedText);
        $this->emptyOwnerFeed = new OwnerFeed();
    }

    public function testEmptyFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->emptyOwnerFeed->extensionElements));
        $this->assertTrue(count($this->emptyOwnerFeed->extensionElements) == 0);
    }

    public function testEmptyFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->emptyOwnerFeed->extensionAttributes));
        $this->assertTrue(count($this->emptyOwnerFeed->extensionAttributes) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->ownerFeed->extensionElements));
        $this->assertTrue(count($this->ownerFeed->extensionElements) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->ownerFeed->extensionAttributes));
        $this->assertTrue(count($this->ownerFeed->extensionAttributes) == 0);
    }

    /**
      * Convert sample feed to XML then back to objects. Ensure that
      * all objects are instances of OwnerEntry and object count matches.
      */
    public function testXmlImportAndOutputAreNonDestructive()
    {
        $entryCount = 0;
        foreach ($this->ownerFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof OwnerEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->ownerFeed and convert back to objects */
        $newOwnerFeed = new OwnerFeed(
                $this->ownerFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newOwnerFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof OwnerEntry);
        }
        $this->assertEquals($entryCount, $newEntryCount);
    }

    /**
      * Ensure that there number of owner entries equals the number
      * of owners defined in the sample file.
      */
    public function testAllEntriesInFeedAreInstantiated()
    {
        //TODO feeds implementing ArrayAccess would be helpful here
        $entryCount = 0;
        foreach ($this->ownerFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(2, $entryCount);
    }

}
