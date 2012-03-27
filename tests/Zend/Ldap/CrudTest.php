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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Ldap;
use Zend\Ldap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class CrudTest extends OnlineTestCase
{
    public function testAddAndDelete()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array(
            'ou' => 'TestCreated',
            'objectClass' => 'organizationalUnit'
        );
        try {
            $this->_getLDAP()->add($dn, $data);
            $this->assertEquals(1, $this->_getLDAP()->count('ou=TestCreated'));
            $this->_getLDAP()->delete($dn);
            $this->assertEquals(0, $this->_getLDAP()->count('ou=TestCreated'));
        } catch (Ldap\Exception $e) {
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
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
            $this->_getLDAP()->add($dn, $data);
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
            $entry['l']='mylocation2';
            $this->_getLDAP()->update($dn, $entry);
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->_getLDAP()->delete($dn);
            $this->assertEquals('mylocation2', $entry['l'][0]);
        } catch (Ldap\Exception $e) {
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testIllegalAdd()
    {
        $dn=$this->_createDn('ou=TestCreated,ou=Node2,');
        $data=array(
            'ou' => 'TestCreated',
            'objectClass' => 'organizationalUnit'
        );
        $this->_getLDAP()->add($dn, $data);
        $this->_getLDAP()->delete($dn);
    }

    public function testIllegalUpdate()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array(
            'ou' => 'TestCreated',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->_getLDAP()->add($dn, $data);
            $entry=$this->_getLDAP()->getEntry($dn);
            $entry['objectclass'][]='inetOrgPerson';

            $exThrown=false;
            try {
                $this->_getLDAP()->update($dn, $entry);
            }
            catch (Ldap\Exception $e) {
               $exThrown=true;
            }
            $this->_getLDAP()->delete($dn);
            if (!$exThrown) $this->fail('no exception thrown while illegaly updating entry');
        }
        catch (Ldap\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testIllegalDelete()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $this->_getLDAP()->delete($dn);
    }

    public function testDeleteRecursively()
    {
        $topDn=$this->_createDn('ou=RecursiveTest,');
        $dn=$topDn;
        $data=array('ou' => 'RecursiveTest', 'objectclass' => 'organizationalUnit'
        );
        $this->_getLDAP()->add($dn, $data);
        for ($level=1; $level<=5; $level++) {
            $name='Level' . $level;
            $dn='ou=' . $name . ',' . $dn;
            $data=array('ou' => $name, 'objectclass' => 'organizationalUnit');
            $this->_getLDAP()->add($dn, $data);
            for ($item=1; $item<=5; $item++) {
                $uid='Item' . $item;
                $idn='ou=' . $uid . ',' . $dn;
                $idata=array('ou' => $uid, 'objectclass' => 'organizationalUnit');
                $this->_getLDAP()->add($idn, $idata);
            }
        }

        $exCaught=false;
        try {
            $this->_getLDAP()->delete($topDn, false);
        } catch (Ldap\Exception $e) {
            $exCaught=true;
        }
        $this->assertTrue($exCaught,
            'Execption not raised when deleting item with children without specifiying recursive delete');
        $this->_getLDAP()->delete($topDn, true);
        $this->assertFalse($this->_getLDAP()->exists($topDn));
    }

    public function testSave()
    {
        $dn=$this->_createDn('ou=TestCreated,');
        $data=array('ou' => 'TestCreated', 'objectclass' => 'organizationalUnit');
        try {
            $this->_getLDAP()->save($dn, $data);
            $this->assertTrue($this->_getLDAP()->exists($dn));
            $data['l']='mylocation1';
            $this->_getLDAP()->save($dn, $data);
            $this->assertTrue($this->_getLDAP()->exists($dn));
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->_getLDAP()->delete($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
        } catch (Ldap\Exception $e) {
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }

    }

    public function testPrepareLDAPEntryArray()
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
        Ldap\Ldap::prepareLDAPEntryArray($data);
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
     * @group ZF-7888
     */
    public function testZeroValueMakesItThroughSanitationProcess()
    {
        $data = array(
            'string'       => '0',
            'integer'      => 0,
            'stringArray'  => array('0'),
            'integerArray' => array(0),
            'null'         => null,
            'empty'        => '',
            'nullArray'    => array(null),
            'emptyArray'   => array(''),
        );
        Ldap\Ldap::prepareLDAPEntryArray($data);
        $expected=array(
            'string'       => array('0'),
            'integer'      => array('0'),
            'stringarray'  => array('0'),
            'integerarray' => array('0'),
            'null'         => array(),
            'empty'        => array(),
            'nullarray'    => array(),
            'emptyarray'   => array()
        );
        $this->assertEquals($expected, $data);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrepareLDAPEntryArrayArrayData()
    {
        $data=array(
            'a1' => array(array('account')));
        Ldap\Ldap::prepareLDAPEntryArray($data);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrepareLDAPEntryArrayObjectData()
    {
        $class=new \stdClass();
        $class->a='b';
        $data=array(
            'a1' => array($class));
        Ldap\Ldap::prepareLDAPEntryArray($data);
    }

    public function testAddWithDnObject()
    {
        $dn=Ldap\Dn::fromString($this->_createDn('ou=TestCreated,'));
        $data=array(
            'ou' => 'TestCreated',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->_getLDAP()->add($dn, $data);
            $this->assertEquals(1, $this->_getLDAP()->count('ou=TestCreated'));
            $this->_getLDAP()->delete($dn);
        }
        catch (Ldap\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testUpdateWithDnObject()
    {
        $dn=Ldap\Dn::fromString($this->_createDn('ou=TestCreated,'));
        $data=array(
            'ou' => 'TestCreated',
            'l' => 'mylocation1',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->_getLDAP()->add($dn, $data);
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
            $entry['l']='mylocation2';
            $this->_getLDAP()->update($dn, $entry);
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->_getLDAP()->delete($dn);
            $this->assertEquals('mylocation2', $entry['l'][0]);
        }
        catch (Ldap\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSaveWithDnObject()
    {
        $dn=Ldap\Dn::fromString($this->_createDn('ou=TestCreated,'));
        $data=array('ou' => 'TestCreated', 'objectclass' => 'organizationalUnit');
        try {
            $this->_getLDAP()->save($dn, $data);
            $this->assertTrue($this->_getLDAP()->exists($dn));
            $data['l']='mylocation1';
            $this->_getLDAP()->save($dn, $data);
            $this->assertTrue($this->_getLDAP()->exists($dn));
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->_getLDAP()->delete($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
        } catch (Ldap\Exception $e) {
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
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
            $this->_getLDAP()->add($dn, $data);
            $entry=$this->_getLDAP()->getEntry($dn);
            $entry['objectclass'][]='domainRelatedObject';
            $entry['associatedDomain'][]='domain';
            $this->_getLDAP()->update($dn, $entry);
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->_getLDAP()->delete($dn);

            $this->assertEquals('domain', $entry['associateddomain'][0]);
            $this->assertContains('organizationalUnit', $entry['objectclass']);
            $this->assertContains('domainRelatedObject', $entry['objectclass']);
        } catch (Ldap\Exception $e) {
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
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
            $this->_getLDAP()->add($dn, $data);
            $entry=$this->_getLDAP()->getEntry($dn);
            $entry['objectclass']='organizationalUnit';
            $entry['associatedDomain']=null;
            $this->_getLDAP()->update($dn, $entry);
            $entry=$this->_getLDAP()->getEntry($dn);
            $this->_getLDAP()->delete($dn);

            $this->assertArrayNotHasKey('associateddomain', $entry);
            $this->assertContains('organizationalUnit', $entry['objectclass']);
            $this->assertNotContains('domainRelatedObject', $entry['objectclass']);
        } catch (Ldap\Exception $e) {
            if ($this->_getLDAP()->exists($dn)) {
                $this->_getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testAddingEntryWithMissingRdnAttribute() 
    {
        $dn   = $this->_createDn('ou=TestCreated,');
        $data = array(
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry = $this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated'), $entry['ou']);

        } catch (Ldap\Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testAddingEntryWithMissingRdnAttributeValue() 
    {
        $dn   = $this->_createDn('ou=TestCreated,');
        $data = array(
            'ou' => array('SecondOu'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry = $this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Ldap\Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testAddingEntryThatHasMultipleValuesOnRdnAttribute() 
    {
        $dn   = $this->_createDn('ou=TestCreated,');
        $data = array(
            'ou' => array('TestCreated', 'SecondOu'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry = $this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Ldap\Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testUpdatingEntryWithAttributeThatIsAnRdnAttribute() 
    {
        $dn   = $this->_createDn('ou=TestCreated,');
        $data = array(
            'ou' => array('TestCreated'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry = $this->_getLdap()->getEntry($dn);

            $data = array('ou' => array_merge($entry['ou'], array('SecondOu')));
            $this->_getLdap()->update($dn, $data);
            $entry = $this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Ldap\Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testUpdatingEntryWithRdnAttributeValueMissingInData() 
    {
        $dn   = $this->_createDn('ou=TestCreated,');
        $data = array(
            'ou' => array('TestCreated'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->_getLdap()->add($dn, $data);
            $entry = $this->_getLdap()->getEntry($dn);

            $data = array('ou' => 'SecondOu');
            $this->_getLdap()->update($dn, $data);
            $entry = $this->_getLdap()->getEntry($dn);
            $this->_getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Ldap\Exception $e) {
            if ($this->_getLdap()->exists($dn)) {
                $this->_getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }
}
