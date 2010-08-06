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
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Ldap;
use Zend\Ldap;

/**
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_LDAP
 */
class AttributeTest extends \PHPUnit_Framework_TestCase
{
    protected function _assertLocalDateTimeString($timestamp, $value)
    {
        $this->assertEquals(date('YmdHisO', $timestamp), $value);
    }

    protected function _assertUtcDateTimeString($localTimestamp, $value)
    {
        $localOffset = date('Z', $localTimestamp);
        $utcTimestamp = $localTimestamp - $localOffset;
        $this->assertEquals(date('YmdHis', $utcTimestamp) . 'Z', $value);
    }

    public function testGetAttributeValue()
    {
        $data=array('uid' => array('value'));
        $value=Ldap\Attribute::getAttribute($data, 'uid', 0);
        $this->assertEquals('value', $value);
    }

    public function testGetNonExistentAttributeValue()
    {
        $data=array('uid' => array('value'));
        $value=Ldap\Attribute::getAttribute($data, 'uid', 1);
        $this->assertNull($value);
    }

    public function testGetNonExistentAttribute()
    {
        $data=array('uid' => array('value'));
        $value=Ldap\Attribute::getAttribute($data, 'uid2', 0);
        $this->assertNull($value);
        $array=Ldap\Attribute::getAttribute($data, 'uid2');
        $this->assertType('array', $array);
        $this->assertEquals(0, count($array));
    }

    public function testGetAttributeWithWrongIndexType()
    {
        $data=array('uid' => array('value'));
        $value=Ldap\Attribute::getAttribute($data, 'uid', 'index');
        $this->assertNull($value);
        $value=Ldap\Attribute::getAttribute($data, 'uid', 3.1415);
        $this->assertNull($value);
    }

    public function testGetAttributeArray()
    {
        $data=array('uid' => array('value'));
        $value=Ldap\Attribute::getAttribute($data, 'uid');
        $this->assertType('array', $value);
        $this->assertEquals(1, count($value));
        $this->assertContains('value', $value);
    }

    public function testSimpleSetAttribute()
    {
        $data=array();
        Ldap\Attribute::setAttribute($data, 'uid', 'new', false);
        $this->assertArrayHasKey('uid', $data);
        $this->assertType('array', $data['uid']);
        $this->assertEquals(1, count($data['uid']));
        $this->assertContains('new', $data['uid']);
    }

    public function testSimpleOverwriteAttribute()
    {
        $data=array('uid' => array('old'));
        Ldap\Attribute::setAttribute($data, 'uid', 'new', false);
        $this->assertArrayHasKey('uid', $data);
        $this->assertType('array', $data['uid']);
        $this->assertEquals(1, count($data['uid']));
        $this->assertContains('new', $data['uid']);
    }

    public function testSimpleAppendAttribute()
    {
        $data=array('uid' => array('old'));
        Ldap\Attribute::setAttribute($data, 'uid', 'new', true);
        $this->assertArrayHasKey('uid', $data);
        $this->assertType('array', $data['uid']);
        $this->assertEquals(2, count($data['uid']));
        $this->assertContains('old', $data['uid']);
        $this->assertContains('new', $data['uid']);
        $this->assertEquals('old', $data['uid'][0]);
        $this->assertEquals('new', $data['uid'][1]);
    }

    public function testBooleanAttributeHandling()
    {
        $data=array(
            'p1_true' => array('TRUE'),
            'p1_false' => array('FALSE')
        );
        Ldap\Attribute::setAttribute($data, 'p2_true', true);
        Ldap\Attribute::setAttribute($data, 'p2_false', false);
        $this->assertEquals('TRUE', $data['p2_true'][0]);
        $this->assertEquals('FALSE', $data['p2_false'][0]);
        $this->assertEquals(true, Ldap\Attribute::getAttribute($data, 'p1_true', 0));
        $this->assertEquals(false, Ldap\Attribute::getAttribute($data, 'p1_false', 0));
    }

    public function testArraySetAttribute()
    {
        $data=array();
        Ldap\Attribute::setAttribute($data, 'uid', array('new1', 'new2'), false);
        $this->assertArrayHasKey('uid', $data);
        $this->assertType('array', $data['uid']);
        $this->assertEquals(2, count($data['uid']));
        $this->assertContains('new1', $data['uid']);
        $this->assertContains('new2', $data['uid']);
        $this->assertEquals('new1', $data['uid'][0]);
        $this->assertEquals('new2', $data['uid'][1]);
    }

    public function testArrayOverwriteAttribute()
    {
        $data=array('uid' => array('old'));
        Ldap\Attribute::setAttribute($data, 'uid', array('new1', 'new2'), false);
        $this->assertArrayHasKey('uid', $data);
        $this->assertType('array', $data['uid']);
        $this->assertEquals(2, count($data['uid']));
        $this->assertContains('new1', $data['uid']);
        $this->assertContains('new2', $data['uid']);
        $this->assertEquals('new1', $data['uid'][0]);
        $this->assertEquals('new2', $data['uid'][1]);
    }

    public function testArrayAppendAttribute()
    {
        $data=array('uid' => array('old'));
        Ldap\Attribute::setAttribute($data, 'uid', array('new1', 'new2'), true);
        $this->assertArrayHasKey('uid', $data);
        $this->assertType('array', $data['uid']);
        $this->assertEquals(3, count($data['uid']));
        $this->assertContains('old', $data['uid']);
        $this->assertContains('new1', $data['uid']);
        $this->assertContains('new2', $data['uid']);
        $this->assertEquals('old', $data['uid'][0]);
        $this->assertEquals('new1', $data['uid'][1]);
        $this->assertEquals('new2', $data['uid'][2]);
    }

    public function testPasswordSettingSHA()
    {
        $data=array();
        Ldap\Attribute::setPassword($data, 'pa$$w0rd', Ldap\Attribute::PASSWORD_HASH_SHA);
        $password=Ldap\Attribute::getAttribute($data, 'userPassword', 0);
        $this->assertEquals('{SHA}vi3X+3ptD4ulrdErXo+3W72mRyE=', $password);
    }

    public function testPasswordSettingMD5()
    {
        $data=array();
        Ldap\Attribute::setPassword($data, 'pa$$w0rd', Ldap\Attribute::PASSWORD_HASH_MD5);
        $password=Ldap\Attribute::getAttribute($data, 'userPassword', 0);
        $this->assertEquals('{MD5}bJuLJ96h3bhF+WqiVnxnVA==', $password);
    }

    public function testPasswordSettingUnicodePwd()
    {
        $data=array();
        Ldap\Attribute::setPassword($data, 'new', Ldap\Attribute::PASSWORD_UNICODEPWD);
        $password=Ldap\Attribute::getAttribute($data, 'unicodePwd', 0);
        $this->assertEquals("\x22\x00\x6E\x00\x65\x00\x77\x00\x22\x00", $password);
    }

    public function testPasswordSettingCustomAttribute()
    {
        $data=array();
        Ldap\Attribute::setPassword($data, 'pa$$w0rd',
            Ldap\Attribute::PASSWORD_HASH_SHA, 'myAttribute');
        $password=Ldap\Attribute::getAttribute($data, 'myAttribute', 0);
        $this->assertNotNull($password);
    }

    public function testSetAttributeWithObject()
    {
        $data=array();
        $object=new \stdClass();
        $object->a=1;
        $object->b=1.23;
        $object->c='string';
        Ldap\Attribute::setAttribute($data, 'object', $object);
        $this->assertEquals(serialize($object), $data['object'][0]);
    }

    public function testSetAttributeWithFilestream()
    {
        $data=array();
        $stream=fopen(__DIR__ . '/_files/AttributeTest.input.txt', 'r');
        Ldap\Attribute::setAttribute($data, 'file', $stream);
        fclose($stream);
        $this->assertEquals('String from file', $data['file'][0]);
    }

    public function testSetDateTimeValueLocal()
    {
        $ts=mktime(12, 30, 30, 6, 25, 2008);
        $data=array();
        Ldap\Attribute::setDateTimeAttribute($data, 'ts', $ts, false);
        $this->_assertLocalDateTimeString($ts, $data['ts'][0]);
    }

    public function testSetDateTimeValueUtc()
    {
        $ts=mktime(12, 30, 30, 6, 25, 2008);
        $data=array();
        Ldap\Attribute::setDateTimeAttribute($data, 'ts', $ts, true);
        $this->_assertUtcDateTimeString($ts, $data['ts'][0]);
    }

    public function testSetDateTimeValueLocalArray()
    {
        $ts=array();
        $ts[]=mktime(12, 30, 30, 6, 25, 2008);
        $ts[]=mktime(1, 25, 30, 1, 2, 2008);
        $data=array();
        Ldap\Attribute::setDateTimeAttribute($data, 'ts', $ts, false);
        $this->_assertLocalDateTimeString($ts[0], $data['ts'][0]);
        $this->_assertLocalDateTimeString($ts[1], $data['ts'][1]);
    }

    public function testSetDateTimeValueIllegal()
    {
        $ts='dummy';
        $data=array();
        Ldap\Attribute::setDateTimeAttribute($data, 'ts', $ts, false);
        $this->assertEquals(0, count($data['ts']));
    }

    public function testGetDateTimeValueFromLocal()
    {
        $ts=mktime(12, 30, 30, 6, 25, 2008);
        $data=array();
        Ldap\Attribute::setDateTimeAttribute($data, 'ts', $ts, false);
        $this->_assertLocalDateTimeString($ts, $data['ts'][0]);
        $retTs=Ldap\Attribute::getDateTimeAttribute($data, 'ts', 0);
        $this->assertEquals($ts, $retTs);
    }

    public function testGetDateTimeValueFromUtc()
    {
        $ts=mktime(12, 30, 30, 6, 25, 2008);
        $data=array();
        Ldap\Attribute::setDateTimeAttribute($data, 'ts', $ts, true);
        $this->_assertUtcDateTimeString($ts, $data['ts'][0]);
        $retTs=Ldap\Attribute::getDateTimeAttribute($data, 'ts', 0);
        $this->assertEquals($ts, $retTs);
    }

    public function testGetDateTimeValueFromArray()
    {
        $ts=array();
        $ts[]=mktime(12, 30, 30, 6, 25, 2008);
        $ts[]=mktime(1, 25, 30, 1, 2, 2008);
        $data=array();
        Ldap\Attribute::setDateTimeAttribute($data, 'ts', $ts, false);
        $this->_assertLocalDateTimeString($ts[0], $data['ts'][0]);
        $this->_assertLocalDateTimeString($ts[1], $data['ts'][1]);
        $retTs=Ldap\Attribute::getDateTimeAttribute($data, 'ts');
        $this->assertEquals($ts[0], $retTs[0]);
        $this->assertEquals($ts[1], $retTs[1]);
    }

    public function testGetDateTimeValueIllegal()
    {
        $data=array('ts' => array('dummy'));
        $retTs=Ldap\Attribute::getDateTimeAttribute($data, 'ts', 0);
        $this->assertEquals('dummy', $retTs);
    }

    public function testGetDateTimeValueNegativeOffet()
    {
        $data=array('ts' => array('20080612143045-0700'));
        $retTs=Ldap\Attribute::getDateTimeAttribute($data, 'ts', 0);
        $tsCompare=gmmktime(21, 30, 45, 6, 12, 2008);
        $this->assertEquals($tsCompare, $retTs);
    }

    public function testGetDateTimeValueNegativeOffet2()
    {
        $data=array('ts' => array('20080612143045-0715'));
        $retTs=Ldap\Attribute::getDateTimeAttribute($data, 'ts', 0);
        $tsCompare=gmmktime(21, 45, 45, 6, 12, 2008);
        $this->assertEquals($tsCompare, $retTs);
    }

    public function testRemoveAttributeValueSimple()
    {
        $data=array('test' => array('value1', 'value2', 'value3', 'value3'));
        Ldap\Attribute::removeFromAttribute($data, 'test', 'value2');
        $this->assertArrayHasKey('test', $data);
        $this->assertType('array', $data['test']);
        $this->assertEquals(3, count($data['test']));
        $this->assertContains('value1', $data['test']);
        $this->assertContains('value3', $data['test']);
        $this->assertNotContains('value2', $data['test']);
    }

    public function testRemoveAttributeValueArray()
    {
        $data=array('test' => array('value1', 'value2', 'value3', 'value3'));
        Ldap\Attribute::removeFromAttribute($data, 'test', array('value1', 'value2'));
        $this->assertArrayHasKey('test', $data);
        $this->assertType('array', $data['test']);
        $this->assertEquals(2, count($data['test']));
        $this->assertContains('value3', $data['test']);
        $this->assertNotContains('value1', $data['test']);
        $this->assertNotContains('value2', $data['test']);
    }

    public function testRemoveAttributeMultipleValueSimple()
    {
        $data=array('test' => array('value1', 'value2', 'value3', 'value3'));
        Ldap\Attribute::removeFromAttribute($data, 'test', 'value3');
        $this->assertArrayHasKey('test', $data);
        $this->assertType('array', $data['test']);
        $this->assertEquals(2, count($data['test']));
        $this->assertContains('value1', $data['test']);
        $this->assertContains('value2', $data['test']);
        $this->assertNotContains('value3', $data['test']);
    }

    public function testRemoveAttributeMultipleValueArray()
    {
        $data=array('test' => array('value1', 'value2', 'value3', 'value3'));
        Ldap\Attribute::removeFromAttribute($data, 'test', array('value1', 'value3'));
        $this->assertArrayHasKey('test', $data);
        $this->assertType('array', $data['test']);
        $this->assertEquals(1, count($data['test']));
        $this->assertContains('value2', $data['test']);
        $this->assertNotContains('value1', $data['test']);
        $this->assertNotContains('value3', $data['test']);
    }

    public function testRemoveAttributeValueBoolean()
    {
        $data=array('test' => array('TRUE', 'FALSE', 'TRUE', 'FALSE'));
        Ldap\Attribute::removeFromAttribute($data, 'test', false);
        $this->assertArrayHasKey('test', $data);
        $this->assertType('array', $data['test']);
        $this->assertEquals(2, count($data['test']));
        $this->assertContains('TRUE', $data['test']);
        $this->assertNotContains('FALSE', $data['test']);
    }

    public function testRemoveAttributeValueInteger()
    {
        $data=array('test' => array('1', '2', '3', '4'));
        Ldap\Attribute::removeFromAttribute($data, 'test', array(2, 4));
        $this->assertArrayHasKey('test', $data);
        $this->assertType('array', $data['test']);
        $this->assertEquals(2, count($data['test']));
        $this->assertContains('1', $data['test']);
        $this->assertContains('3', $data['test']);
        $this->assertNotContains('2', $data['test']);
        $this->assertNotContains('4', $data['test']);
    }

    public function testConvertFromLDAPValue()
    {
        $this->assertEquals(true, Ldap\Attribute::convertFromLDAPValue('TRUE'));
        $this->assertEquals(false, Ldap\Attribute::convertFromLDAPValue('FALSE'));
    }

    public function testConvertToLDAPValue()
    {
        $this->assertEquals('string', Ldap\Attribute::convertToLDAPValue('string'));
        $this->assertEquals('1', Ldap\Attribute::convertToLDAPValue(1));
        $this->assertEquals('TRUE', Ldap\Attribute::convertToLDAPValue(true));
    }

    public function testConvertFromLDAPDateTimeValue()
    {
        $ldap='20080625123030+0200';
        $this->assertEquals(gmmktime(10, 30, 30, 6, 25, 2008),
            Ldap\Attribute::convertFromLDAPDateTimeValue($ldap));
    }

    public function testConvertToLDAPDateTimeValue()
    {
        $ts=mktime(12, 30, 30, 6, 25, 2008);
        $this->_assertLocalDateTimeString($ts, Ldap\Attribute::convertToLDAPDateTimeValue($ts));
    }

    public function testRemoveDuplicates()
    {
        $data=array(
            'strings1' => array('value1', 'value2', 'value2', 'value3'),
            'strings2' => array('value1', 'value2', 'value3', 'value4'),
            'boolean1' => array('TRUE', 'TRUE', 'TRUE', 'TRUE'),
            'boolean2' => array('TRUE', 'FALSE', 'TRUE', 'FALSE'),
        );
        $expected=array(
            'strings1' => array('value1', 'value2', 'value3'),
            'strings2' => array('value1', 'value2', 'value3', 'value4'),
            'boolean1' => array('TRUE'),
            'boolean2' => array('TRUE', 'FALSE'),
        );
        Ldap\Attribute::removeDuplicatesFromAttribute($data, 'strings1');
        Ldap\Attribute::removeDuplicatesFromAttribute($data, 'strings2');
        Ldap\Attribute::removeDuplicatesFromAttribute($data, 'boolean1');
        Ldap\Attribute::removeDuplicatesFromAttribute($data, 'boolean2');
        $this->assertEquals($expected, $data);
    }

    public function testHasValue()
    {
        $data=array(
            'strings1' => array('value1', 'value2', 'value2', 'value3'),
            'strings2' => array('value1', 'value2', 'value3', 'value4'),
            'boolean1' => array('TRUE', 'TRUE', 'TRUE', 'TRUE'),
            'boolean2' => array('TRUE', 'FALSE', 'TRUE', 'FALSE'),
        );

        $this->assertTrue(Ldap\Attribute::attributeHasValue($data, 'strings1', 'value1'));
        $this->assertFalse(Ldap\Attribute::attributeHasValue($data, 'strings1', 'value4'));
        $this->assertTrue(Ldap\Attribute::attributeHasValue($data, 'boolean1', true));
        $this->assertFalse(Ldap\Attribute::attributeHasValue($data, 'boolean1', false));

        $this->assertTrue(Ldap\Attribute::attributeHasValue($data, 'strings1',
            array('value1', 'value2')));
        $this->assertTrue(Ldap\Attribute::attributeHasValue($data, 'strings1',
            array('value1', 'value2', 'value3')));
        $this->assertFalse(Ldap\Attribute::attributeHasValue($data, 'strings1',
            array('value1', 'value2', 'value3', 'value4')));
        $this->assertTrue(Ldap\Attribute::attributeHasValue($data, 'strings2',
            array('value1', 'value2', 'value3', 'value4')));

        $this->assertTrue(Ldap\Attribute::attributeHasValue($data, 'boolean2',
            array(true, false)));
        $this->assertFalse(Ldap\Attribute::attributeHasValue($data, 'boolean1',
            array(true, false)));
    }

    public function testPasswordGenerationSSHA()
    {
        $password = 'pa$$w0rd';
        $ssha = Ldap\Attribute::createPassword($password, Ldap\Attribute::PASSWORD_HASH_SSHA);
        $encoded = substr($ssha, strpos($ssha, '}'));
        $binary  = base64_decode($encoded);
        $this->assertEquals(24, strlen($binary));
        $hash    = substr($binary, 0, 20);
        $salt    = substr($binary, 20);
        $this->assertEquals(4, strlen($salt));
        $this->assertEquals(sha1($password . $salt, true), $hash);
    }

    public function testPasswordGenerationSHA()
    {
        $password = 'pa$$w0rd';
        $sha = Ldap\Attribute::createPassword($password, Ldap\Attribute::PASSWORD_HASH_SHA);
        $encoded = substr($sha, strpos($sha, '}'));
        $binary  = base64_decode($encoded);
        $this->assertEquals(20, strlen($binary));
        $this->assertEquals(sha1($password, true), $binary);
    }

    public function testPasswordGenerationSMD5()
    {
        $password = 'pa$$w0rd';
        $smd5 = Ldap\Attribute::createPassword($password, Ldap\Attribute::PASSWORD_HASH_SMD5);
        $encoded = substr($smd5, strpos($smd5, '}'));
        $binary  = base64_decode($encoded);
        $this->assertEquals(20, strlen($binary));
        $hash    = substr($binary, 0, 16);
        $salt    = substr($binary, 16);
        $this->assertEquals(4, strlen($salt));
        $this->assertEquals(md5($password . $salt, true), $hash);
    }

    public function testPasswordGenerationMD5()
    {
        $password = 'pa$$w0rd';
        $md5 = Ldap\Attribute::createPassword($password, Ldap\Attribute::PASSWORD_HASH_MD5);
        $encoded = substr($md5, strpos($md5, '}'));
        $binary  = base64_decode($encoded);
        $this->assertEquals(16, strlen($binary));
        $this->assertEquals(md5($password, true), $binary);
    }

    public function testPasswordGenerationUnicodePwd()
    {
        $password = 'new';
        $unicodePwd = Ldap\Attribute::createPassword($password, Ldap\Attribute::PASSWORD_UNICODEPWD);
        $this->assertEquals(10, strlen($unicodePwd));
        $this->assertEquals("\x22\x00\x6E\x00\x65\x00\x77\x00\x22\x00", $unicodePwd);
    }
}

