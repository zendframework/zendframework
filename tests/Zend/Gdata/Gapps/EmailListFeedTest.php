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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Gapps.php';
require_once 'Zend/Gdata/Gapps/EmailListFeed.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Gapps_EmailListFeedTest extends PHPUnit_Framework_TestCase
{
    protected $emailListFeed = null;

    /**
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $emailListFeedText = file_get_contents(
                'Zend/Gdata/Gapps/_files/EmailListFeedDataSample1.xml',
                true);
        $this->emailListFeed = new Zend_Gdata_Gapps_EmailListFeed($emailListFeedText);
        $this->emptyEmailListFeed = new Zend_Gdata_Gapps_EmailListFeed();
    }

    public function testEmptyFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->emptyEmailListFeed->extensionElements));
        $this->assertTrue(count($this->emptyEmailListFeed->extensionElements) == 0);
    }

    public function testEmptyFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->emptyEmailListFeed->extensionAttributes));
        $this->assertTrue(count($this->emptyEmailListFeed->extensionAttributes) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->emailListFeed->extensionElements));
        $this->assertTrue(count($this->emailListFeed->extensionElements) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->emailListFeed->extensionAttributes));
        $this->assertTrue(count($this->emailListFeed->extensionAttributes) == 0);
    }

    /**
      * Convert sample feed to XML then back to objects. Ensure that
      * all objects are instances of EventEntry and object count matches.
      */
    public function testXmlImportAndOutputAreNonDestructive()
    {
        $entryCount = 0;
        foreach ($this->emailListFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Gapps_EmailListEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->emailListFeed and convert back to objects */
        $newEmailListFeed = new Zend_Gdata_Gapps_EmailListFeed(
                $this->emailListFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newEmailListFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Gapps_EmailListEntry);
        }
        $this->assertEquals($entryCount, $newEntryCount);
    }

    /**
      * Ensure that there number of lsit feeds equals the number
      * of calendars defined in the sample file.
      */
    public function testAllEntriesInFeedAreInstantiated()
    {
        //TODO feeds implementing ArrayAccess would be helpful here
        $entryCount = 0;
        foreach ($this->emailListFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(2, $entryCount);
    }

}
