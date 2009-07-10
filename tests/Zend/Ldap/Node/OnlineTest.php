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
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'OnlineTestCase.php';
/**
 * @see Zend_Ldap_Node
 */
require_once 'Zend/Ldap/Node.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Ldap_Node_OnlineTest extends Zend_Ldap_OnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->_prepareLdapServer();
    }

    protected function tearDown()
    {
        $this->_cleanupLdapServer();
        parent::tearDown();
    }

    public function testLoadFromLdap()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
        $this->assertType('Zend_Ldap_Node', $node);
        $this->assertTrue($node->isAttached());
    }

    public function testChangeReadOnlySystemAttributes()
    {
        $node=$this->_getLdap()->getBaseNode();
        try {
            $node->setAttribute('createTimestamp', false);
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node->createTimestamp=false;
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node['createTimestamp']=false;
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node->appendToAttribute('createTimestamp', 'value');
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $rdn=$node->getRdnArray(Zend_Ldap_Dn::ATTR_CASEFOLD_LOWER);
            $attr=key($rdn);
            $node->deleteAttribute($attr);
            $this->fail('Expected exception for modification of read-only attribute ' . $attr);
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Cannot change attribute because it\'s part of the RDN', $e->getMessage());
        }
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testLoadFromLdapIllegalEntry()
    {
        $dn=$this->_createDn('ou=Test99,');
        $node=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
    }

    public function testDetachAndReattach()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
        $this->assertType('Zend_Ldap_Node', $node);
        $this->assertTrue($node->isAttached());
        $node->detachLdap();
        $this->assertFalse($node->isAttached());
        $node->attachLdap($this->_getLdap());
        $this->assertTrue($node->isAttached());
    }

    public function testSerialize()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
        $sdata=serialize($node);
        $newObject=unserialize($sdata);
        $this->assertFalse($newObject->isAttached());
        $this->assertTrue($node->isAttached());
        $this->assertEquals($sdata, serialize($newObject));
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testAttachToInvalidLdap()
    {
        $data=array(
            'dn'          => 'ou=name,dc=example,dc=org',
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node=Zend_Ldap_Node::fromArray($data);
        $this->assertFalse($node->isAttached());
        $node->attachLdap($this->_getLdap());
    }

    public function testAttachToValidLdap()
    {
        $data=array(
            'dn'          => $this->_createDn('ou=name,'),
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node=Zend_Ldap_Node::fromArray($data);
        $this->assertFalse($node->isAttached());
        $node->attachLdap($this->_getLdap());
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
        $node1=Zend_Ldap_Node::fromArray($data);
        $node1->attachLdap($this->_getLdap());
        $this->assertFalse($node1->exists());
        $dn=$this->_createDn('ou=Test1,');
        $node2=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
        $this->assertTrue($node2->exists());
    }

    public function testReload()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
        $node->reload();
        $this->assertEquals($dn, $node->getDn()->toString());
        $this->assertEquals('ou=Test1', $node->getRdnString());
    }

    public function testGetNode()
    {
        $dn=$this->_createDn('ou=Test1,');
        $node=$this->_getLdap()->getNode($dn);
        $this->assertEquals($dn, $node->getDn()->toString());
        $this->assertEquals("Test1", $node->getAttribute('ou', 0));
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testGetIllegalNode()
    {
        $dn=$this->_createDn('ou=Test99,');
        $node=$this->_getLdap()->getNode($dn);
    }

    public function testGetBaseNode()
    {
        $node=$this->_getLdap()->getBaseNode();
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $node->getDnString());

        $dn=Zend_Ldap_Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Zend_Ldap_Dn::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dn[0]['ou'], $node->getAttribute('ou', 0));
    }

    public function testSearchSubtree()
    {
        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $items=$node->searchSubtree('(objectClass=organizationalUnit)', Zend_Ldap::SEARCH_SCOPE_SUB,
            array(), 'ou');
        $this->assertType('Zend_Ldap_Node_Collection', $items);
        $this->assertEquals(3, $items->count());

        $i=0;
        $dns=array(
            $this->_createDn('ou=Node,'),
            $this->_createDn('ou=Test1,ou=Node,'),
            $this->_createDn('ou=Test2,ou=Node,'));
        foreach ($items as $key => $node) {
            $key=Zend_Ldap_Dn::fromString($key)->toString(Zend_Ldap_Dn::ATTR_CASEFOLD_LOWER);
            $this->assertEquals($dns[$i], $key);
            if ($i === 0) {
                $this->assertEquals('Node', $node->ou[0]);
            } else {
                $this->assertEquals('Test' . $i, $node->ou[0]);
            }
            $this->assertEquals($key, $node->getDnString(Zend_Ldap_Dn::ATTR_CASEFOLD_LOWER));
            $i++;
        }
        $this->assertEquals(3, $i);
    }

    public function testCountSubtree()
    {
        $node=$this->_getLdap()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(9, $node->countSubtree('(objectClass=organizationalUnit)',
            Zend_Ldap::SEARCH_SCOPE_SUB));
    }

    public function testCountChildren()
    {
        $node=$this->_getLdap()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(6, $node->countChildren());
        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $this->assertEquals(2, $node->countChildren());
    }

    public function testSearchChildren()
    {
        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $this->assertEquals(2, $node->searchChildren('(objectClass=*)', array(), 'ou')->count());
        $node=$this->_getLdap()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(6, $node->searchChildren('(objectClass=*)', array(), 'ou')->count());
    }

    public function testGetParent()
    {
        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $pnode=$node->getParent();
        $this->assertEquals(Zend_Ldap_Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE)
            ->toString(Zend_Ldap_Dn::ATTR_CASEFOLD_LOWER),
            $pnode->getDnString(Zend_Ldap_Dn::ATTR_CASEFOLD_LOWER));
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testGetNonexistantParent()
    {
        $node=$this->_getLdap()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $pnode=$node->getParent();
    }

    public function testLoadFromLdapWithDnObject()
    {
        $dn=Zend_Ldap_Dn::fromString($this->_createDn('ou=Test1,'));
        $node=Zend_Ldap_Node::fromLdap($dn, $this->_getLdap());
        $this->assertType('Zend_Ldap_Node', $node);
        $this->assertTrue($node->isAttached());
    }
}