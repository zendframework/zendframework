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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Ldap_OnlineTestCase
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'OnlineTestCase.php';
/**
 * @see Zend_Ldap_Node
 */
require_once 'Zend/Ldap/Node.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class Zend_Ldap_Node_UpdateTest extends Zend_Ldap_OnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->_prepareLdapServer();
    }

    protected function tearDown()
    {
        foreach ($this->_getLdap()->getBaseNode()->searchChildren('objectClass=*') as $child) {
            $this->_getLdap()->delete($child->getDn(), true);
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
        $node1=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
        $node1->l='f';
        $node1->update();

        $this->assertTrue($this->_getLdap()->exists($dn));
        $node2=$this->_getLdap()->getEntry($dn);
        $this->_stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1=$node1->getData(false);
        $this->_stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testAddNewNode()
    {
        $dn=$this->_createDn('ou=Test,');
        $node1=Zend_Ldap_Node::create($dn, array('organizationalUnit'));
        $node1->l='a';
        $node1->update($this->_getLdap());

        $this->assertTrue($this->_getLdap()->exists($dn));
        $node2=$this->_getLdap()->getEntry($dn);
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
        $node1=Zend_Ldap_Node::fromLdap($dnOld, $this->_getLdap());
        $node1->l='f';
        $node1->setDn($dnNew);
        $node1->update();

        $this->assertFalse($this->_getLdap()->exists($dnOld));
        $this->assertTrue($this->_getLdap()->exists($dnNew));
        $node2=$this->_getLdap()->getEntry($dnNew);
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
        $node1=Zend_Ldap_Node::create($dnOld, array('organizationalUnit'));
        $node1->l='a';
        $node1->setDn($dnNew);
        $node1->update($this->_getLdap());

        $this->assertFalse($this->_getLdap()->exists($dnOld));
        $this->assertTrue($this->_getLdap()->exists($dnNew));
        $node2=$this->_getLdap()->getEntry($dnNew);
        $this->_stripActiveDirectorySystemAttributes($node2);
        unset($node2['dn']);
        $node1=$node1->getData(false);
        $this->_stripActiveDirectorySystemAttributes($node1);
        $this->assertEquals($node2, $node1);
    }

    public function testModifyDeletedNode()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node1=Zend_Ldap_Node::create($dn, array('organizationalUnit'));
        $node1->delete();
        $node1->update($this->_getLdap());

        $this->assertFalse($this->_getLdap()->exists($dn));

        $node1->l='a';
        $node1->update();

        $this->assertFalse($this->_getLdap()->exists($dn));
    }

    public function testAddDeletedNode()
    {
        $dn=$this->_createDn('ou=Test,');
        $node1=Zend_Ldap_Node::create($dn, array('organizationalUnit'));
        $node1->delete();
        $node1->update($this->_getLdap());

        $this->assertFalse($this->_getLdap()->exists($dn));
    }

    public function testMoveDeletedExistingNode()
    {
        $dnOld=$this->_createDn('ou=Test1,');
        $dnNew=$this->_createDn('ou=Test,');
        $node1=Zend_Ldap_Node::fromLdap($dnOld, $this->_getLdap());
        $node1->setDn($dnNew);
        $node1->delete();
        $node1->update();

        $this->assertFalse($this->_getLdap()->exists($dnOld));
        $this->assertFalse($this->_getLdap()->exists($dnNew));
    }

    public function testMoveDeletedNewNode()
    {
        $dnOld=$this->_createDn('ou=Test,');
        $dnNew=$this->_createDn('ou=TestNew,');
        $node1=Zend_Ldap_Node::create($dnOld, array('organizationalUnit'));
        $node1->setDn($dnNew);
        $node1->delete();
        $node1->update($this->_getLdap());

        $this->assertFalse($this->_getLdap()->exists($dnOld));
        $this->assertFalse($this->_getLdap()->exists($dnNew));
    }

    public function testMoveNode()
    {
        $dnOld=$this->_createDn('ou=Test1,');
        $dnNew=$this->_createDn('ou=Test,');

        $node=Zend_Ldap_Node::fromLdap($dnOld, $this->_getLdap());
        $node->setDn($dnNew);
        $node->update();
        $this->assertFalse($this->_getLdap()->exists($dnOld));
        $this->assertTrue($this->_getLdap()->exists($dnNew));

        $node=Zend_Ldap_Node::fromLdap($dnNew, $this->_getLdap());
        $node->move($dnOld);
        $node->update();
        $this->assertFalse($this->_getLdap()->exists($dnNew));
        $this->assertTrue($this->_getLdap()->exists($dnOld));

        $node=Zend_Ldap_Node::fromLdap($dnOld, $this->_getLdap());
        $node->rename($dnNew);
        $node->update();
        $this->assertFalse($this->_getLdap()->exists($dnOld));
        $this->assertTrue($this->_getLdap()->exists($dnNew));
    }
}
