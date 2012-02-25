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
namespace ZendTest\Ldap\Node;
use Zend\Ldap\Node;


/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class UpdateTest extends \ZendTest\Ldap\OnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->_prepareLDAPServer();
    }

    protected function tearDown()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            return;
        }

        foreach ($this->_getLDAP()->getBaseNode()->searchChildren('objectClass=*') as $child) {
            $this->_getLDAP()->delete($child->getDn(), true);
        }

        parent::tearDown();
    }

    protected function _stripActiveDirectorySystemAttributes(&$entry)
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
                $entry['objectclass']=array_merge(array('top'), $entry['objectclass']);
            }
        }
    }

    public function testSimpleUpdateOneValue()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node1=Node::fromLDAP($dn, $this->_getLDAP());
        $node1->l='f';
        $node1->update();

        $this->assertTrue($this->_getLDAP()->exists($dn));
        $node2=$this->_getLDAP()->getEntry($dn);
        $this->_stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1=$node1->getData(false);
        $this->_stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testAddNewNode()
    {
        $dn=$this->_createDn('ou=Test,');
        $node1=Node::create($dn, array('organizationalUnit'));
        $node1->l='a';
        $node1->update($this->_getLDAP());

        $this->assertTrue($this->_getLDAP()->exists($dn));
        $node2=$this->_getLDAP()->getEntry($dn);
        $this->_stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1=$node1->getData(false);
        $this->_stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testMoveExistingNode()
    {
        $dnOld=$this->_createDn('ou=Test1,');
        $dnNew=$this->_createDn('ou=Test,');
        $node1=Node::fromLDAP($dnOld, $this->_getLDAP());
        $node1->l='f';
        $node1->setDn($dnNew);
        $node1->update();

        $this->assertFalse($this->_getLDAP()->exists($dnOld));
        $this->assertTrue($this->_getLDAP()->exists($dnNew));
        $node2=$this->_getLDAP()->getEntry($dnNew);
        $this->_stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1=$node1->getData(false);
        $this->_stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testMoveNewNode()
    {
        $dnOld=$this->_createDn('ou=Test,');
        $dnNew=$this->_createDn('ou=TestNew,');
        $node1=Node::create($dnOld, array('organizationalUnit'));
        $node1->l='a';
        $node1->setDn($dnNew);
        $node1->update($this->_getLDAP());

        $this->assertFalse($this->_getLDAP()->exists($dnOld));
        $this->assertTrue($this->_getLDAP()->exists($dnNew));
        $node2=$this->_getLDAP()->getEntry($dnNew);
        $this->_stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1=$node1->getData(false);
        $this->_stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testModifyDeletedNode()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node1=Node::create($dn, array('organizationalUnit'));
        $node1->delete();
        $node1->update($this->_getLDAP());

        $this->assertFalse($this->_getLDAP()->exists($dn));

        $node1->l='a';
        $node1->update();

        $this->assertFalse($this->_getLDAP()->exists($dn));
    }

    public function testAddDeletedNode()
    {
        $dn=$this->_createDn('ou=Test,');
        $node1=Node::create($dn, array('organizationalUnit'));
        $node1->delete();
        $node1->update($this->_getLDAP());

        $this->assertFalse($this->_getLDAP()->exists($dn));
    }

    public function testMoveDeletedExistingNode()
    {
        $dnOld=$this->_createDn('ou=Test1,');
        $dnNew=$this->_createDn('ou=Test,');
        $node1=Node::fromLDAP($dnOld, $this->_getLDAP());
        $node1->setDn($dnNew);
        $node1->delete();
        $node1->update();

        $this->assertFalse($this->_getLDAP()->exists($dnOld));
        $this->assertFalse($this->_getLDAP()->exists($dnNew));
    }

    public function testMoveDeletedNewNode()
    {
        $dnOld=$this->_createDn('ou=Test,');
        $dnNew=$this->_createDn('ou=TestNew,');
        $node1=Node::create($dnOld, array('organizationalUnit'));
        $node1->setDn($dnNew);
        $node1->delete();
        $node1->update($this->_getLDAP());

        $this->assertFalse($this->_getLDAP()->exists($dnOld));
        $this->assertFalse($this->_getLDAP()->exists($dnNew));
    }

    public function testMoveNode()
    {
        $dnOld=$this->_createDn('ou=Test1,');
        $dnNew=$this->_createDn('ou=Test,');

        $node=Node::fromLDAP($dnOld, $this->_getLDAP());
        $node->setDn($dnNew);
        $node->update();
        $this->assertFalse($this->_getLDAP()->exists($dnOld));
        $this->assertTrue($this->_getLDAP()->exists($dnNew));

        $node=Node::fromLDAP($dnNew, $this->_getLDAP());
        $node->move($dnOld);
        $node->update();
        $this->assertFalse($this->_getLDAP()->exists($dnNew));
        $this->assertTrue($this->_getLDAP()->exists($dnOld));

        $node=Node::fromLDAP($dnOld, $this->_getLDAP());
        $node->rename($dnNew);
        $node->update();
        $this->assertFalse($this->_getLDAP()->exists($dnOld));
        $this->assertTrue($this->_getLDAP()->exists($dnNew));
    }
}
