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
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\GApps;
use Zend\GData\GApps;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class UserFeedTest extends \PHPUnit_Framework_TestCase
{
    protected $userFeed = null;

    /**
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $userFeedText = file_get_contents(
                'Zend/GData/GApps/_files/UserFeedDataSample1.xml',
                true);
        $this->userFeed = new GApps\UserFeed($userFeedText);
        $this->emptyUserFeed = new GApps\UserFeed();
    }

    public function testEmptyFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->emptyUserFeed->extensionElements));
        $this->assertTrue(count($this->emptyUserFeed->extensionElements) == 0);
    }

    public function testEmptyFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->emptyUserFeed->extensionAttributes));
        $this->assertTrue(count($this->emptyUserFeed->extensionAttributes) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->userFeed->extensionElements));
        $this->assertTrue(count($this->userFeed->extensionElements) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->userFeed->extensionAttributes));
        $this->assertTrue(count($this->userFeed->extensionAttributes) == 0);
    }

    /**
      * Convert sample feed to XML then back to objects. Ensure that
      * all objects are instances of EventEntry and object count matches.
      */
    public function testXmlImportAndOutputAreNonDestructive()
    {
        $entryCount = 0;
        foreach ($this->userFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof GApps\UserEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->userFeed and convert back to objects */
        $newUserFeed = new GApps\UserFeed(
                $this->userFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newUserFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof GApps\UserEntry);
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
        foreach ($this->userFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(2, $entryCount);
    }

}
