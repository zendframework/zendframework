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
 * @package    Zend_GData_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Health;
use Zend\GData\Health;

/**
 * @category   Zend
 * @package    Zend_GData_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Health
 */
class ProfileFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->profileFeed = new Health\ProfileFeed();
        $this->feedText = file_get_contents(
            'Zend/GData/Health/_files/TestDataHealthProfileFeedSample.xml', true);
    }

    private function verifyAllSamplePropertiesAreCorrect($profileFeed) {
        $this->assertEquals('https://www.google.com/health/feeds/profile/default', $profileFeed->id->text);
        $this->assertEquals('2008-09-30T01:07:17.888Z', $profileFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $profileFeed->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/health/kinds#profile', $profileFeed->category[0]->term);
        $this->assertEquals('text', $profileFeed->title->type);
        $this->assertEquals('Profile Feed', $profileFeed->title->text);
        $this->assertEquals('self', $profileFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $profileFeed->getLink('self')->type);
        $this->assertEquals('https://www.google.com/health/feeds/profile/default?digest=false',
            $profileFeed->getLink('self')->href);
        $this->assertEquals(1, $profileFeed->startIndex->text);
    }

    public function testAllSamplePropertiesAreCorrect() {
        $this->profileFeed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->profileFeed);
    }

    public function testToAndFromXMLString()
    {
        $this->assertEquals(0, count($this->profileFeed->entry));

        $this->profileFeed->transferFromXML($this->feedText);
        $this->assertEquals(15, count($this->profileFeed->entry));
        foreach($this->profileFeed->entry as $entry)
        {
            $this->assertTrue($entry instanceof Health\ProfileEntry);
        }

        $newProfileFeed = new Health\ProfileFeed();
        $doc = new \DOMDocument();
        $doc->loadXML($this->profileFeed->saveXML());
        $newProfileFeed->transferFromDom($doc->documentElement);

        $this->assertEquals(15, count($newProfileFeed->entry));
        foreach($newProfileFeed->entry as $entry)
        {
            $this->assertTrue($entry instanceof Health\ProfileEntry);
        }
    }


    public function testGetEntries()
    {
        $this->profileFeed->transferFromXML($this->feedText);
        $entries = $this->profileFeed->getEntries();
        $this->assertTrue(is_array($entries));
        $this->assertEquals(15, count($entries));
    }

    public function testGetAllCcrFromProfileEntries()
    {
        $newProfileFeed = new Health\ProfileFeed();
        $newProfileFeed->transferFromXML($this->feedText);
        foreach($newProfileFeed->entry as $entry)
        {
            $ccr = $entry->getCcr();
            $this->assertTrue($ccr instanceof \Zend\GData\Health\Extension\Ccr);
        }
    }

    public function testGetFirstEntrysCcrMedication()
    {
        $this->profileFeed->transferFromXML($this->feedText);

        $medications = $this->profileFeed->entry[0]->getCcr()->getMedications();
        $this->assertInstanceOf('DOMNodeList', $medications);
        $this->assertEquals(1, count($medications));

        foreach ($medications as $med) {
          $xmlStr = $med->ownerDocument->saveXML($med);
          $this->assertXmlStringEqualsXmlString(file_get_contents(
              'Zend/GData/Health/_files/TestDataHealthProfileEntrySample_medications3.xml', true),
              $xmlStr);
        }
    }
}

