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
use Zend\GData\Health\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Health
 */
class ProfileEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->entry = new Health\ProfileEntry();
        $this->entryText = file_get_contents(
            'Zend/GData/Health/_files/TestDataHealthProfileEntrySample.xml',
            true);
    }

    public function testEmptyProfileEntry()
    {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
        $this->assertTrue($this->entry->getCcr() === null);
    }

    public function testEmptyProfileEntryToAndFromStringShouldMatch() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newProfileEntry = new Health\ProfileEntry();
        $newProfileEntry->transferFromXML($entryXml);
        $newProfileEntryXML = $newProfileEntry->saveXML();
        $this->assertTrue($entryXml == $newProfileEntryXML);
    }

    public function testGetAllCcrFromProfileEntry()
    {
        $this->entry->transferFromXML($this->entryText);
        $ccr = $this->entry->getCcr();
        $this->assertTrue($ccr instanceof Extension\Ccr);
        $this->assertXmlStringEqualsXmlString(file_get_contents(
            'Zend/GData/Health/_files/TestDataHealthProfileEntrySample_just_ccr.xml', true), $ccr->getXML());
    }

    public function testSetCcrInProfileEntry()
    {
        $this->entry->transferFromXML($this->entryText);
        $ccrXML = file_get_contents(
            'Zend/GData/Health/_files/TestDataHealthProfileEntrySample_just_ccr.xml', true);
        $ccrElement = $this->entry->setCcr($ccrXML);
        $this->assertTrue($ccrElement instanceof Extension\Ccr);
        $this->assertXmlStringEqualsXmlString(file_get_contents(
            'Zend/GData/Health/_files/TestDataHealthProfileEntrySample_just_ccr.xml', true), $this->entry->getCcr()->getXML());
    }

    /*
     *  These functions test the magic _call method within Zend_GData_Health_Extension_Ccr
     */
    public function testGetCcrMedicationsFromProfileEntry()
    {
        $this->entry->transferFromXML($this->entryText);
        $medications = $this->entry->getCcr()->getMedications();
        $this->assertEquals(1, count($medications));
        foreach ($medications as $med) {
          $this->assertXmlStringEqualsXmlString(file_get_contents(
              "Zend/GData/Health/_files/TestDataHealthProfileEntrySample_medications_all.xml", true),
              $med->ownerDocument->saveXML($med));
        }
    }

    public function testGetCcrConditionsFromProfileEntry()
    {
        $this->entry->transferFromXML($this->entryText);
        $problems = $this->entry->getCcr()->getProblems();
        $conditions = $this->entry->getCcr()->getConditions();
        $this->assertEquals($problems, $conditions);

        $this->assertEquals(1, count($conditions));
        foreach ($conditions as $index => $condition) {
            $this->assertXmlStringEqualsXmlString(file_get_contents(
                "Zend/GData/Health/_files/TestDataHealthProfileEntrySample_condition_all.xml", true),
                $condition->ownerDocument->saveXML($condition));
        }
    }

    public function testGetCcrAllerigiesFromProfileEntry()
    {
        $this->entry->transferFromXML($this->entryText);
        $allergies = $this->entry->getCcr()->getAllergies();
        $alerts = $this->entry->getCcr()->getAlerts();
        $this->assertEquals($allergies, $alerts);

        $this->assertEquals(1, count($alerts));
        foreach ($alerts as $index => $alert) {
            $this->assertXmlStringEqualsXmlString(file_get_contents(
                "Zend/GData/Health/_files/TestDataHealthProfileEntrySample_allergy_all.xml", true),
                $alert->ownerDocument->saveXML($alert));
        }
    }

    public function testGetCcrLabResultsFromProfileEntry()
    {
        $this->entry->transferFromXML($this->entryText);
        $labresults = $this->entry->getCcr()->getLabResults();
        $results = $this->entry->getCcr()->getResults();
        $this->assertEquals($labresults, $results);

        $this->assertEquals(1, count($results));
        foreach ($results as $index => $result) {
            $this->assertXmlStringEqualsXmlString(file_get_contents(
                "Zend/GData/Health/_files/TestDataHealthProfileEntrySample_results0.xml", true),
                $result->ownerDocument->saveXML($result));
        }
    }
}

