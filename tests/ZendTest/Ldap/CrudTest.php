<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap;

use Zend\Ldap;
use Zend\Ldap\Exception;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 */
class CrudTest extends AbstractOnlineTestCase
{
    public function testAddAndDelete()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => 'TestCreated',
            'objectClass' => 'organizationalUnit'
        );
        try {
            $this->getLDAP()->add($dn, $data);
            $this->assertEquals(1, $this->getLDAP()->count('ou=TestCreated'));
            $this->getLDAP()->delete($dn);
            $this->assertEquals(0, $this->getLDAP()->count('ou=TestCreated'));
        } catch (Exception\LdapException $e) {
            if ($this->getLDAP()->exists($dn)) {
                $this->getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testUpdate()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => 'TestCreated',
            'l'           => 'mylocation1',
            'objectClass' => 'organizationalUnit'
        );
        try {
            $this->getLDAP()->add($dn, $data);
            $entry = $this->getLDAP()->getEntry($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
            $entry['l'] = 'mylocation2';
            $this->getLDAP()->update($dn, $entry);
            $entry = $this->getLDAP()->getEntry($dn);
            $this->getLDAP()->delete($dn);
            $this->assertEquals('mylocation2', $entry['l'][0]);
        } catch (Exception\LdapException $e) {
            if ($this->getLDAP()->exists($dn)) {
                $this->getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testIllegalAdd()
    {
        $dn   = $this->createDn('ou=TestCreated,ou=Node2,');
        $data = array(
            'ou'          => 'TestCreated',
            'objectClass' => 'organizationalUnit'
        );
        $this->getLDAP()->add($dn, $data);
        $this->getLDAP()->delete($dn);
    }

    public function testIllegalUpdate()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => 'TestCreated',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->getLDAP()->add($dn, $data);
            $entry                  = $this->getLDAP()->getEntry($dn);
            $entry['objectclass'][] = 'inetOrgPerson';

            $exThrown = false;
            try {
                $this->getLDAP()->update($dn, $entry);
            } catch (Exception\LdapException $e) {
                $exThrown = true;
            }
            $this->getLDAP()->delete($dn);
            if (!$exThrown) {
                $this->fail('no exception thrown while illegaly updating entry');
            }
        } catch (Exception\LdapException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testIllegalDelete()
    {
        $dn = $this->createDn('ou=TestCreated,');
        $this->getLDAP()->delete($dn);
    }

    public function testDeleteRecursively()
    {
        $topDn = $this->createDn('ou=RecursiveTest,');
        $dn    = $topDn;
        $data  = array('ou'          => 'RecursiveTest',
                       'objectclass' => 'organizationalUnit'
        );
        $this->getLDAP()->add($dn, $data);
        for ($level = 1; $level <= 5; $level++) {
            $name = 'Level' . $level;
            $dn   = 'ou=' . $name . ',' . $dn;
            $data = array('ou'          => $name,
                          'objectclass' => 'organizationalUnit');
            $this->getLDAP()->add($dn, $data);
            for ($item = 1; $item <= 5; $item++) {
                $uid   = 'Item' . $item;
                $idn   = 'ou=' . $uid . ',' . $dn;
                $idata = array('ou'          => $uid,
                               'objectclass' => 'organizationalUnit');
                $this->getLDAP()->add($idn, $idata);
            }
        }

        $exCaught = false;
        try {
            $this->getLDAP()->delete($topDn, false);
        } catch (Exception\LdapException $e) {
            $exCaught = true;
        }
        $this->assertTrue($exCaught,
            'Execption not raised when deleting item with children without specifiying recursive delete'
        );
        $this->getLDAP()->delete($topDn, true);
        $this->assertFalse($this->getLDAP()->exists($topDn));
    }

    public function testSave()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array('ou'          => 'TestCreated',
                      'objectclass' => 'organizationalUnit');
        try {
            $this->getLDAP()->save($dn, $data);
            $this->assertTrue($this->getLDAP()->exists($dn));
            $data['l'] = 'mylocation1';
            $this->getLDAP()->save($dn, $data);
            $this->assertTrue($this->getLDAP()->exists($dn));
            $entry = $this->getLDAP()->getEntry($dn);
            $this->getLDAP()->delete($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
        } catch (Exception\LdapException $e) {
            if ($this->getLDAP()->exists($dn)) {
                $this->getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }

    }

    public function testPrepareLDAPEntryArray()
    {
        $data = array(
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
        $expected = array(
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
        $expected = array(
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
        $data = array(
            'a1' => array(array('account')));
        Ldap\Ldap::prepareLDAPEntryArray($data);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrepareLDAPEntryArrayObjectData()
    {
        $class    = new \stdClass();
        $class->a = 'b';
        $data     = array(
            'a1' => array($class));
        Ldap\Ldap::prepareLDAPEntryArray($data);
    }

    public function testAddWithDnObject()
    {
        $dn   = Ldap\Dn::fromString($this->createDn('ou=TestCreated,'));
        $data = array(
            'ou'          => 'TestCreated',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->getLDAP()->add($dn, $data);
            $this->assertEquals(1, $this->getLDAP()->count('ou=TestCreated'));
            $this->getLDAP()->delete($dn);
        } catch (Exception\LdapException $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testUpdateWithDnObject()
    {
        $dn   = Ldap\Dn::fromString($this->createDn('ou=TestCreated,'));
        $data = array(
            'ou'          => 'TestCreated',
            'l'           => 'mylocation1',
            'objectclass' => 'organizationalUnit'
        );
        try {
            $this->getLDAP()->add($dn, $data);
            $entry = $this->getLDAP()->getEntry($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
            $entry['l'] = 'mylocation2';
            $this->getLDAP()->update($dn, $entry);
            $entry = $this->getLDAP()->getEntry($dn);
            $this->getLDAP()->delete($dn);
            $this->assertEquals('mylocation2', $entry['l'][0]);
        } catch (Exception\LdapException $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSaveWithDnObject()
    {
        $dn   = Ldap\Dn::fromString($this->createDn('ou=TestCreated,'));
        $data = array('ou'          => 'TestCreated',
                      'objectclass' => 'organizationalUnit');
        try {
            $this->getLDAP()->save($dn, $data);
            $this->assertTrue($this->getLDAP()->exists($dn));
            $data['l'] = 'mylocation1';
            $this->getLDAP()->save($dn, $data);
            $this->assertTrue($this->getLDAP()->exists($dn));
            $entry = $this->getLDAP()->getEntry($dn);
            $this->getLDAP()->delete($dn);
            $this->assertEquals('mylocation1', $entry['l'][0]);
        } catch (Exception\LdapException $e) {
            if ($this->getLDAP()->exists($dn)) {
                $this->getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testAddObjectClass()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => 'TestCreated',
            'l'           => 'mylocation1',
            'objectClass' => 'organizationalUnit'
        );
        try {
            $this->getLDAP()->add($dn, $data);
            $entry                       = $this->getLDAP()->getEntry($dn);
            $entry['objectclass'][]      = 'domainRelatedObject';
            $entry['associatedDomain'][] = 'domain';
            $this->getLDAP()->update($dn, $entry);
            $entry = $this->getLDAP()->getEntry($dn);
            $this->getLDAP()->delete($dn);

            $this->assertEquals('domain', $entry['associateddomain'][0]);
            $this->assertContains('organizationalUnit', $entry['objectclass']);
            $this->assertContains('domainRelatedObject', $entry['objectclass']);
        } catch (Exception\LdapException $e) {
            if ($this->getLDAP()->exists($dn)) {
                $this->getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    public function testRemoveObjectClass()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'associatedDomain' => 'domain',
            'ou'               => 'TestCreated',
            'l'                => 'mylocation1',
            'objectClass'      => array('organizationalUnit', 'domainRelatedObject')
        );
        try {
            $this->getLDAP()->add($dn, $data);
            $entry                     = $this->getLDAP()->getEntry($dn);
            $entry['objectclass']      = 'organizationalUnit';
            $entry['associatedDomain'] = null;
            $this->getLDAP()->update($dn, $entry);
            $entry = $this->getLDAP()->getEntry($dn);
            $this->getLDAP()->delete($dn);

            $this->assertArrayNotHasKey('associateddomain', $entry);
            $this->assertContains('organizationalUnit', $entry['objectclass']);
            $this->assertNotContains('domainRelatedObject', $entry['objectclass']);
        } catch (Exception\LdapException $e) {
            if ($this->getLDAP()->exists($dn)) {
                $this->getLDAP()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testAddingEntryWithMissingRdnAttribute()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->getLdap()->add($dn, $data);
            $entry = $this->getLdap()->getEntry($dn);
            $this->getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated'), $entry['ou']);

        } catch (Exception\LdapException $e) {
            if ($this->getLdap()->exists($dn)) {
                $this->getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testAddingEntryWithMissingRdnAttributeValue()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => array('SecondOu'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->getLdap()->add($dn, $data);
            $entry = $this->getLdap()->getEntry($dn);
            $this->getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Exception\LdapException $e) {
            if ($this->getLdap()->exists($dn)) {
                $this->getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testAddingEntryThatHasMultipleValuesOnRdnAttribute()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => array('TestCreated', 'SecondOu'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->getLdap()->add($dn, $data);
            $entry = $this->getLdap()->getEntry($dn);
            $this->getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Exception\LdapException $e) {
            if ($this->getLdap()->exists($dn)) {
                $this->getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testUpdatingEntryWithAttributeThatIsAnRdnAttribute()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => array('TestCreated'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->getLdap()->add($dn, $data);
            $entry = $this->getLdap()->getEntry($dn);

            $data = array('ou' => array_merge($entry['ou'], array('SecondOu')));
            $this->getLdap()->update($dn, $data);
            $entry = $this->getLdap()->getEntry($dn);
            $this->getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Exception\LdapException $e) {
            if ($this->getLdap()->exists($dn)) {
                $this->getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }

    /**
     * @group ZF-9564
     */
    public function testUpdatingEntryWithRdnAttributeValueMissingInData()
    {
        $dn   = $this->createDn('ou=TestCreated,');
        $data = array(
            'ou'          => array('TestCreated'),
            'objectClass' => array('organizationalUnit')
        );
        try {
            $this->getLdap()->add($dn, $data);
            $entry = $this->getLdap()->getEntry($dn);

            $data = array('ou' => 'SecondOu');
            $this->getLdap()->update($dn, $data);
            $entry = $this->getLdap()->getEntry($dn);
            $this->getLdap()->delete($dn);
            $this->assertEquals(array('TestCreated', 'SecondOu'), $entry['ou']);

        } catch (Exception\LdapException $e) {
            if ($this->getLdap()->exists($dn)) {
                $this->getLdap()->delete($dn);
            }
            $this->fail($e->getMessage());
        }
    }
}
