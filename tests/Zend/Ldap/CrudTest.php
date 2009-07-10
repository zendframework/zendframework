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
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Ldap_OnlineTestCase
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'OnlineTestCase.php';

/**
 * @see Zend_Ldap_Dn
 */
require_once 'Zend/Ldap/Dn.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Ldap_CrudTest extends Zend_Ldap_OnlineTestCase
{
    public function testAddAndDelete()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array(
            'ou' => 'TestCreated',
            'objectClass' => 'organizationalUnit'
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $this->assertEquals(1, $this->_getLdap()->count('ou=TestCreated'));
            $this->_getLdap()->delete($dn);
            $this->assertEquals(0, $this->_getLdap()->count('ou=TestCreated'));
        } catch (Zend_Ldap_Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testUpdate()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array(
            'ou' => 'TestCreated',
            'l' => 'mylocation1',
            'objectClass' => 'organizationalUnit'
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry=$this->_getLdap()->getEntry($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
            $entry['l']='mylocation2';
            $this->_getLdap()->update($dn, $entry);
            $entry=$this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals('mylocation2', $entry['l'][0]);
        } catch (Zend_Ldap_Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testIllegalAdd()
    {
        $dn=$this->_createDn('ou=TestCreated,ou=Node2,');
        $data=array(
            'ou' => 'TestCreated',
            'objectClass' => 'organizationalUnit'
        );
        $this->_getLdap()->add($dn, $data);
        $this->_getLdap()->delete($dn);
    }

    public function testIllegalUpdate()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array(
            'ou' => 'TestCreated',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry=$this->_getLdap()->getEntry($dn);
            $entry['objectclass'][]='inetOrgPerson';

            $exThrown=false;
            try {
                $this->_getLdap()->update($dn, $entry);
            }
            catch (Zend_Ldap_Exception $e) {
               $exThrown=true;
            }
            $this->_getLdap()->delete($dn);
            if (!$exThrown) $this->fail('no exception thrown while illegaly updating entry');
        }
        catch (Zend_Ldap_Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testIllegalDelete()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $this->_getLdap()->delete($dn);
    }

    public function testDeleteRecursively()
    {
        $topDn=$this->_createDn('ou=RecursiveTest,');
        $dn=$topDn;
        $data=array('ou' => 'RecursiveTest', 'objectclass' => 'organizationalUnit'
        );
        $this->_getLdap()->add($dn, $data);
        for ($level=1; $level<=5; $level++) {
            $name='Level' . $level;
            $dn='ou=' . $name . ',' . $dn;
            $data=array('ou' => $name, 'objectclass' => 'organizationalUnit');
            $this->_getLdap()->add($dn, $data);
            for ($item=1; $item<=5; $item++) {
                $uid='Item' . $item;
                $idn='ou=' . $uid . ',' . $dn;
                $idata=array('ou' => $uid, 'objectclass' => 'organizationalUnit');
                $this->_getLdap()->add($idn, $idata);
            }
        }

        $exCaught=false;
        try {
            $this->_getLdap()->delete($topDn, false);
        } catch (Zend_Ldap_Exception $e) {
            $exCaught=true;
        }
        $this->assertTrue($exCaught,
            'Execption not raised when deleting item with children without specifiying recursive delete');
        $this->_getLdap()->delete($topDn, true);
        $this->assertFalse($this->_getLdap()->exists($topDn));
    }

    public function testSave()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array('ou' => 'TestCreated', 'objectclass' => 'organizationalUnit');
        try {
            $this->_getLdap()->save($dn, $data);
            $this->assertTrue($this->_getLdap()->exists($dn));
            $data['l']='mylocation1';
            $this->_getLdap()->save($dn, $data);
            $this->assertTrue($this->_getLdap()->exists($dn));
            $entry=$this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
        } catch (Zend_Ldap_Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }

    }

    public function testPrepareLdapEntryArray()
    {
        $data=array(
            'a1' => 'TestCreated',
            'a2' => 'account',
            'a3' => null,
            'a4' => '',
            'a5' => array('TestCreated'),
            'a6' => array('account'),
            'a7' => array(null),
            'a8' => array(''),
            'a9' => array('', null, 'account', '', null, 'TestCreated', '', null));
        Zend_Ldap::prepareLdapEntryArray($data);
        $expected=array(
            'a1' => array('TestCreated'),
            'a2' => array('account'),
            'a3' => array(),
            'a4' => array(),
            'a5' => array('TestCreated'),
            'a6' => array('account'),
            'a7' => array(),
            'a8' => array(),
            'a9' => array('account', 'TestCreated'));
        $this->assertEquals($expected, $data);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrepareLdapEntryArrayArrayData()
    {
        $data=array(
            'a1' => array(array('account')));
        Zend_Ldap::prepareLdapEntryArray($data);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrepareLdapEntryArrayObjectData()
    {
        $class=new stdClass();
        $class->a='b';
        $data=array(
            'a1' => array($class));
        Zend_Ldap::prepareLdapEntryArray($data);
    }

    public function testAddWithDnObject()
    {
        $dn=Zend_Ldap_Dn::fromString($this->_createDn('ou=TestCreated,'));
        $data=array(
            'ou' => 'TestCreated',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $this->assertEquals(1, $this->_getLdap()->count('ou=TestCreated'));
            $this->_getLdap()->delete($dn);
        }
        catch (Zend_Ldap_Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testUpdateWithDnObject()
    {
        $dn=Zend_Ldap_Dn::fromString($this->_createDn('ou=TestCreated,'));
        $data=array(
            'ou' => 'TestCreated',
            'l' => 'mylocation1',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry=$this->_getLdap()->getEntry($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
            $entry['l']='mylocation2';
            $this->_getLdap()->update($dn, $entry);
            $entry=$this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals('mylocation2', $entry['l'][0]);
        }
        catch (Zend_Ldap_Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSaveWithDnObject()
    {
        $dn=Zend_Ldap_Dn::fromString($this->_createDn('ou=TestCreated,'));
        $data=array('ou' => 'TestCreated', 'objectclass' => 'organizationalUnit');
        try {
            $this->_getLdap()->save($dn, $data);
            $this->assertTrue($this->_getLdap()->exists($dn));
            $data['l']='mylocation1';
            $this->_getLdap()->save($dn, $data);
            $this->assertTrue($this->_getLdap()->exists($dn));
            $entry=$this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
        } catch (Zend_Ldap_Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testAddObjectClass()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array(
            'ou' => 'TestCreated',
            'l' => 'mylocation1',
            'objectClass' => 'organizationalUnit'
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry=$this->_getLdap()->getEntry($dn);
            $entry['objectclass'][]='domainRelatedObject';
            $entry['associatedDomain'][]='domain';
            $this->_getLdap()->update($dn, $entry);
            $entry=$this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);

            $this->assertEquals('domain', $entry['associateddomain'][0]);
            $this->assertContains('organizationalUnit', $entry['objectclass']);
            $this->assertContains('domainRelatedObject', $entry['objectclass']);
        } catch (Zend_Ldap_Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testRemoveObjectClass()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array(
            'associatedDomain' => 'domain',
            'ou' => 'TestCreated',
            'l' => 'mylocation1',
            'objectClass' => array('organizationalUnit', 'domainRelatedObject')
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry=$this->_getLdap()->getEntry($dn);
            $entry['objectclass']='organizationalUnit';
            $entry['associatedDomain']=null;
            $this->_getLdap()->update($dn, $entry);
            $entry=$this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);

            $this->assertArrayNotHasKey('associateddomain', $entry);
            $this->assertContains('organizationalUnit', $entry['objectclass']);
            $this->assertNotContains('domainRelatedObject', $entry['objectclass']);
        } catch (Zend_Ldap_Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }
}