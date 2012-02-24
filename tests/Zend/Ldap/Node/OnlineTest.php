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
use Zend\Ldap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class OnlineTest extends \ZendTest\Ldap\OnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->_prepareLDAPServer();
    }

    protected function tearDown()
    {
        $this->_cleanupLDAPServer();
        parent::tearDown();
    }

    public function testLoadFromLDAP()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Node::fromLDAP($dn, $this->_getLDAP());
        $this->assertInstanceOf('Zend\Ldap\Node', $node);
        $this->assertTrue($node->isAttached());
    }

    public function testChangeReadOnlySystemAttributes()
    {
        $node=$this->_getLDAP()->getBaseNode();
        try {
            $node->setAttribute('createTimestamp', false);
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Ldap\Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node->createTimestamp=false;
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Ldap\Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node['createTimestamp']=false;
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Ldap\Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node->appendToAttribute('createTimestamp', 'value');
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Ldap\Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $rdn=$node->getRdnArray(Ldap\Dn::ATTR_CASEFOLD_LOWER);
            $attr=key($rdn);
            $node->deleteAttribute($attr);
            $this->fail('Expected exception for modification of read-only attribute ' . $attr);
        } catch (Ldap\Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s part of the RDN', $e->getMessage());
        }
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testLoadFromLDAPIllegalEntry()
    {
        $dn=$this->_createDn('ou=Test99,');
        $node=Node::fromLDAP($dn, $this->_getLDAP());
    }

    public function testDetachAndReattach()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Node::fromLDAP($dn, $this->_getLDAP());
        $this->assertInstanceOf('Zend\Ldap\Node', $node);
        $this->assertTrue($node->isAttached());
        $node->detachLDAP();
        $this->assertFalse($node->isAttached());
        $node->attachLDAP($this->_getLDAP());
        $this->assertTrue($node->isAttached());
    }

    public function testSerialize()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Node::fromLDAP($dn, $this->_getLDAP());
        $sdata=serialize($node);
        $newObject=unserialize($sdata);
        $this->assertFalse($newObject->isAttached());
        $this->assertTrue($node->isAttached());
        $this->assertEquals($sdata, serialize($newObject));
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testAttachToInvalidLDAP()
    {
        $data=array(
            'dn'          => 'ou=name,dc=example,dc=org',
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node=Node::fromArray($data);
        $this->assertFalse($node->isAttached());
        $node->attachLDAP($this->_getLDAP());
    }

    public function testAttachToValidLDAP()
    {
        $data=array(
            'dn'          => $this->_createDn('ou=name,'),
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node=Node::fromArray($data);
        $this->assertFalse($node->isAttached());
        $node->attachLDAP($this->_getLDAP());
        $this->assertTrue($node->isAttached());
    }

    public function testExistsDn()
    {
        $data=array(
            'dn'          => $this->_createDn('ou=name,'),
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node1=Node::fromArray($data);
        $node1->attachLDAP($this->_getLDAP());
        $this->assertFalse($node1->exists());
        $dn=$this->_createDn('ou=Test1,');
        $node2=Node::fromLDAP($dn, $this->_getLDAP());
        $this->assertTrue($node2->exists());
    }

    public function testReload()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Node::fromLDAP($dn, $this->_getLDAP());
        $node->reload();
        $this->assertEquals($dn, $node->getDn()->toString());
        $this->assertEquals('ou=Test1', $node->getRdnString());
    }

    public function testGetNode()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=$this->_getLDAP()->getNode($dn);
        $this->assertEquals($dn, $node->getDn()->toString());
        $this->assertEquals("Test1", $node->getAttribute('ou', 0));
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testGetIllegalNode()
    {
        $dn=$this->_createDn('ou=Test99,');
        $node=$this->_getLDAP()->getNode($dn);
    }

    public function testGetBaseNode()
    {
        $node=$this->_getLDAP()->getBaseNode();
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $node->getDnString());

        $dn=Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Ldap\Dn::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dn[0]['ou'], $node->getAttribute('ou', 0));
    }

    public function testSearchSubtree()
    {
        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $items=$node->searchSubtree('(objectClass=organizationalUnit)', Ldap\Ldap::SEARCH_SCOPE_SUB,
            array(), 'ou');
        $this->assertInstanceOf('Zend\Ldap\Node\Collection', $items);
        $this->assertEquals(3, $items->count());

        $i=0;
        $dns=array(
            $this->_createDn('ou=Node,'),
            $this->_createDn('ou=Test1,ou=Node,'),
            $this->_createDn('ou=Test2,ou=Node,'));
        foreach ($items as $key => $node) {
            $key=Ldap\Dn::fromString($key)->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
            $this->assertEquals($dns[$i], $key);
            if ($i === 0) {
                $this->assertEquals('Node', $node->ou[0]);
            } else {
                $this->assertEquals('Test' . $i, $node->ou[0]);
            }
            $this->assertEquals($key, $node->getDnString(Ldap\Dn::ATTR_CASEFOLD_LOWER));
            $i++;
        }
        $this->assertEquals(3, $i);
    }

    public function testCountSubtree()
    {
        $node=$this->_getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(9, $node->countSubtree('(objectClass=organizationalUnit)',
            Ldap\Ldap::SEARCH_SCOPE_SUB));
    }

    public function testCountChildren()
    {
        $node=$this->_getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(6, $node->countChildren());
        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $this->assertEquals(2, $node->countChildren());
    }

    public function testSearchChildren()
    {
        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $this->assertEquals(2, $node->searchChildren('(objectClass=*)', array(), 'ou')->count());
        $node=$this->_getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(6, $node->searchChildren('(objectClass=*)', array(), 'ou')->count());
    }

    public function testGetParent()
    {
        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $pnode=$node->getParent();
        $this->assertEquals(Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE)
            ->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER),
            $pnode->getDnString(Ldap\Dn::ATTR_CASEFOLD_LOWER));
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testGetNonexistantParent()
    {
        $node=$this->_getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $pnode=$node->getParent();
    }

    public function testLoadFromLDAPWithDnObject()
    {
        $dn=Ldap\Dn::fromString($this->_createDn('ou=Test1,'));
        $node=Node::fromLDAP($dn, $this->_getLDAP());
        $this->assertInstanceOf('Zend\Ldap\Node', $node);
        $this->assertTrue($node->isAttached());
    }
}
