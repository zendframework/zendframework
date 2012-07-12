<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\GApps;

use Zend\GData\GApps\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class QuotaTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->quotaText = file_get_contents(
                'Zend/GData/GApps/_files/QuotaElementSample1.xml',
                true);
        $this->quota = new Extension\Quota();
    }

    public function testEmptyQuotaShouldHaveNoExtensionElements()
    {
        $this->assertTrue(is_array($this->quota->extensionElements));
        $this->assertTrue(count($this->quota->extensionElements) == 0);
    }

    public function testEmptyQuotaShouldHaveNoExtensionAttributes()
    {
        $this->assertTrue(is_array($this->quota->extensionAttributes));
        $this->assertTrue(count($this->quota->extensionAttributes) == 0);
    }

    public function testSampleQuotaShouldHaveNoExtensionElements()
    {
        $this->quota->transferFromXML($this->quotaText);
        $this->assertTrue(is_array($this->quota->extensionElements));
        $this->assertTrue(count($this->quota->extensionElements) == 0);
    }

    public function testSampleQuotaShouldHaveNoExtensionAttributes()
    {
        $this->quota->transferFromXML($this->quotaText);
        $this->assertTrue(is_array($this->quota->extensionAttributes));
        $this->assertTrue(count($this->quota->extensionAttributes) == 0);
    }

    public function testNormalQuotaShouldHaveNoExtensionElements()
    {
        $this->quota->limit = "123456789";

        $this->assertEquals("123456789", $this->quota->limit);

        $this->assertEquals(0, count($this->quota->extensionElements));
        $newQuota = new Extension\Quota();
        $newQuota->transferFromXML($this->quota->saveXML());
        $this->assertEquals(0, count($newQuota->extensionElements));
        $newQuota->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newQuota->extensionElements));
        $this->assertEquals("123456789", $newQuota->limit);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GApps();
        $newQuota2 = $gdata->newQuota();
        $newQuota2->transferFromXML($newQuota->saveXML());
        $this->assertEquals(1, count($newQuota2->extensionElements));
        $this->assertEquals("123456789", $newQuota2->limit);
    }

    public function testEmptyQuotaToAndFromStringShouldMatch()
    {
        $quotaXml = $this->quota->saveXML();
        $newQuota = new Extension\Quota();
        $newQuota->transferFromXML($quotaXml);
        $newQuotaXml = $newQuota->saveXML();
        $this->assertTrue($quotaXml == $newQuotaXml);
    }

    public function testQuotaWithValueToAndFromStringShouldMatch()
    {
        $this->quota->limit = "123456789";
        $quotaXml = $this->quota->saveXML();
        $newQuota = new Extension\Quota();
        $newQuota->transferFromXML($quotaXml);
        $newQuotaXml = $newQuota->saveXML();
        $this->assertTrue($quotaXml == $newQuotaXml);
        $this->assertEquals("123456789", $this->quota->limit);
    }

    public function testExtensionAttributes()
    {
        $extensionAttributes = $this->quota->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->quota->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->quota->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->quota->extensionAttributes['foo2']['value']);
        $quotaXml = $this->quota->saveXML();
        $newQuota = new Extension\Quota();
        $newQuota->transferFromXML($quotaXml);
        $this->assertEquals('bar', $newQuota->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newQuota->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullQuotaToAndFromString()
    {
        $this->quota->transferFromXML($this->quotaText);
        $this->assertEquals("2048", $this->quota->limit);
    }

}
