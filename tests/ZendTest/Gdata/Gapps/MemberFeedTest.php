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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

require_once 'Zend/Gdata/Gapps.php';
require_once 'Zend/Gdata/Gapps/MemberFeed.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class Zend_Gdata_Gapps_MemberFeedTest extends PHPUnit_Framework_TestCase
{
    protected $memberFeed = null;

    /**
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $memberFeedText = file_get_contents(
                'Zend/Gdata/Gapps/_files/MemberFeedDataSample1.xml',
                true);
        $this->memberFeed = new Zend_Gdata_Gapps_MemberFeed($memberFeedText);
        $this->emptyMemberFeed = new Zend_Gdata_Gapps_MemberFeed();
    }

    public function testEmptyFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->emptyMemberFeed->extensionElements));
        $this->assertTrue(count($this->emptyMemberFeed->extensionElements) == 0);
    }

    public function testEmptyFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->emptyMemberFeed->extensionAttributes));
        $this->assertTrue(count($this->emptyMemberFeed->extensionAttributes) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->memberFeed->extensionElements));
        $this->assertTrue(count($this->memberFeed->extensionElements) == 0);
    }

    public function testSampleFeedShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->memberFeed->extensionAttributes));
        $this->assertTrue(count($this->memberFeed->extensionAttributes) == 0);
    }

    /**
      * Convert sample feed to XML then back to objects. Ensure that
      * all objects are instances of MemberEntry and object count matches.
      */
    public function testXmlImportAndOutputAreNonDestructive()
    {
        $entryCount = 0;
        foreach ($this->memberFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Gapps_MemberEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->memberFeed and convert back to objects */
        $newMemberFeed = new Zend_Gdata_Gapps_MemberFeed(
                $this->memberFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newMemberFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Gapps_MemberEntry);
        }
        $this->assertEquals($entryCount, $newEntryCount);
    }

    /**
      * Ensure that there number of member entries equals the number
      * of members defined in the sample file.
      */
    public function testAllEntriesInFeedAreInstantiated()
    {
        //TODO feeds implementing ArrayAccess would be helpful here
        $entryCount = 0;
        foreach ($this->memberFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals(2, $entryCount);
    }

}
