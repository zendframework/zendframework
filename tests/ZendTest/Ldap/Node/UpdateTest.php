<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap\Node;

use Zend\Ldap;
use ZendTest\Ldap as TestLdap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class UpdateTest extends TestLdap\AbstractOnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->prepareLDAPServer();
    }

    protected function tearDown()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            return;
        }

        foreach ($this->getLDAP()->getBaseNode()->searchChildren('objectClass=*') as $child) {
            $this->getLDAP()->delete($child->getDn(), true);
        }

        parent::tearDown();
    }

    protected function stripActiveDirectorySystemAttributes(&$entry)
    {
        $adAttributes = array('distinguishedname', 'instancetype', 'name', 'objectcategory',
                              'objectguid', 'usnchanged', 'usncreated', 'whenchanged', 'whencreated');
        foreach ($adAttributes as $attr) {
            if (array_key_exists($attr, $entry)) {
                unset($entry[$attr]);
            }
        }

        if (array_key_exists('objectclass', $entry) && count($entry['objectclass']) > 0) {
            if ($entry['objectclass'][0] !== 'top') {
                $entry['objectclass'] = array_merge(array('top'), $entry['objectclass']);
            }
        }
    }

    public function testSimpleUpdateOneValue()
    {
        $dn       = $this->createDn('ou=Test1,');
        $node1    = Ldap\Node::fromLDAP($dn, $this->getLDAP());
        $node1->l = 'f';
        $node1->update();

        $this->assertTrue($this->getLDAP()->exists($dn));
        $node2 = $this->getLDAP()->getEntry($dn);
        $this->stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1 = $node1->getData(false);
        $this->stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testAddNewNode()
    {
        $dn       = $this->createDn('ou=Test,');
        $node1    = Ldap\Node::create($dn, array('organizationalUnit'));
        $node1->l = 'a';
        $node1->update($this->getLDAP());

        $this->assertTrue($this->getLDAP()->exists($dn));
        $node2 = $this->getLDAP()->getEntry($dn);
        $this->stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1 = $node1->getData(false);
        $this->stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testMoveExistingNode()
    {
        $dnOld    = $this->createDn('ou=Test1,');
        $dnNew    = $this->createDn('ou=Test,');
        $node1    = Ldap\Node::fromLDAP($dnOld, $this->getLDAP());
        $node1->l = 'f';
        $node1->setDn($dnNew);
        $node1->update();

        $this->assertFalse($this->getLDAP()->exists($dnOld));
        $this->assertTrue($this->getLDAP()->exists($dnNew));
        $node2 = $this->getLDAP()->getEntry($dnNew);
        $this->stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1 = $node1->getData(false);
        $this->stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testMoveNewNode()
    {
        $dnOld    = $this->createDn('ou=Test,');
        $dnNew    = $this->createDn('ou=TestNew,');
        $node1    = Ldap\Node::create($dnOld, array('organizationalUnit'));
        $node1->l = 'a';
        $node1->setDn($dnNew);
        $node1->update($this->getLDAP());

        $this->assertFalse($this->getLDAP()->exists($dnOld));
        $this->assertTrue($this->getLDAP()->exists($dnNew));
        $node2 = $this->getLDAP()->getEntry($dnNew);
        $this->stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1 = $node1->getData(false);
        $this->stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testModifyDeletedNode()
    {
        $dn    = $this->createDn('ou=Test1,');
        $node1 = Ldap\Node::create($dn, array('organizationalUnit'));
        $node1->delete();
        $node1->update($this->getLDAP());

        $this->assertFalse($this->getLDAP()->exists($dn));

        $node1->l = 'a';
        $node1->update();

        $this->assertFalse($this->getLDAP()->exists($dn));
    }

    public function testAddDeletedNode()
    {
        $dn    = $this->createDn('ou=Test,');
        $node1 = Ldap\Node::create($dn, array('organizationalUnit'));
        $node1->delete();
        $node1->update($this->getLDAP());

        $this->assertFalse($this->getLDAP()->exists($dn));
    }

    public function testMoveDeletedExistingNode()
    {
        $dnOld = $this->createDn('ou=Test1,');
        $dnNew = $this->createDn('ou=Test,');
        $node1 = Ldap\Node::fromLDAP($dnOld, $this->getLDAP());
        $node1->setDn($dnNew);
        $node1->delete();
        $node1->update();

        $this->assertFalse($this->getLDAP()->exists($dnOld));
        $this->assertFalse($this->getLDAP()->exists($dnNew));
    }

    public function testMoveDeletedNewNode()
    {
        $dnOld = $this->createDn('ou=Test,');
        $dnNew = $this->createDn('ou=TestNew,');
        $node1 = Ldap\Node::create($dnOld, array('organizationalUnit'));
        $node1->setDn($dnNew);
        $node1->delete();
        $node1->update($this->getLDAP());

        $this->assertFalse($this->getLDAP()->exists($dnOld));
        $this->assertFalse($this->getLDAP()->exists($dnNew));
    }

    public function testMoveNode()
    {
        $dnOld = $this->createDn('ou=Test1,');
        $dnNew = $this->createDn('ou=Test,');

        $node = Ldap\Node::fromLDAP($dnOld, $this->getLDAP());
        $node->setDn($dnNew);
        $node->update();
        $this->assertFalse($this->getLDAP()->exists($dnOld));
        $this->assertTrue($this->getLDAP()->exists($dnNew));

        $node = Ldap\Node::fromLDAP($dnNew, $this->getLDAP());
        $node->move($dnOld);
        $node->update();
        $this->assertFalse($this->getLDAP()->exists($dnNew));
        $this->assertTrue($this->getLDAP()->exists($dnOld));

        $node = Ldap\Node::fromLDAP($dnOld, $this->getLDAP());
        $node->rename($dnNew);
        $node->update();
        $this->assertFalse($this->getLDAP()->exists($dnOld));
        $this->assertTrue($this->getLDAP()->exists($dnNew));
    }
}
