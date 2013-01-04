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
use Zend\Ldap\Exception;
use ZendTest\Ldap as TestLdap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class OnlineTest extends TestLdap\AbstractOnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->prepareLDAPServer();
    }

    protected function tearDown()
    {
        $this->cleanupLDAPServer();
        parent::tearDown();
    }

    public function testLoadFromLDAP()
    {
        $dn   = $this->createDn('ou=Test1,');
        $node = Ldap\Node::fromLDAP($dn, $this->getLDAP());
        $this->assertInstanceOf('Zend\Ldap\Node', $node);
        $this->assertTrue($node->isAttached());
    }

    public function testChangeReadOnlySystemAttributes()
    {
        $node = $this->getLDAP()->getBaseNode();
        try {
            $node->setAttribute('createTimestamp', false);
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Exception\ExceptionInterface $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node->createTimestamp = false;
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Exception\ExceptionInterface $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node['createTimestamp'] = false;
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Exception\ExceptionInterface $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $node->appendToAttribute('createTimestamp', 'value');
            $this->fail('Expected exception for modification of read-only attribute createTimestamp');
        } catch (Exception\ExceptionInterface $e) {
            $this->assertEquals('Cannot change attribute because it\'s read-only', $e->getMessage());
        }
        try {
            $rdn  = $node->getRdnArray(Ldap\Dn::ATTR_CASEFOLD_LOWER);
            $attr = key($rdn);
            $node->deleteAttribute($attr);
            $this->fail('Expected exception for modification of read-only attribute ' . $attr);
        } catch (Exception\ExceptionInterface $e) {
            $this->assertEquals('Cannot change attribute because it\'s part of the RDN', $e->getMessage());
        }
    }

    /**
     * @expectedException Zend\Ldap\Exception\ExceptionInterface
     */
    public function testLoadFromLDAPIllegalEntry()
    {
        $dn   = $this->createDn('ou=Test99,');
        $node = Ldap\Node::fromLDAP($dn, $this->getLDAP());
    }

    public function testDetachAndReattach()
    {
        $dn   = $this->createDn('ou=Test1,');
        $node = Ldap\Node::fromLDAP($dn, $this->getLDAP());
        $this->assertInstanceOf('Zend\Ldap\Node', $node);
        $this->assertTrue($node->isAttached());
        $node->detachLDAP();
        $this->assertFalse($node->isAttached());
        $node->attachLDAP($this->getLDAP());
        $this->assertTrue($node->isAttached());
    }

    public function testSerialize()
    {
        $dn        = $this->createDn('ou=Test1,');
        $node      = Ldap\Node::fromLDAP($dn, $this->getLDAP());
        $sdata     = serialize($node);
        $newObject = unserialize($sdata);
        $this->assertFalse($newObject->isAttached());
        $this->assertTrue($node->isAttached());
        $this->assertEquals($sdata, serialize($newObject));
    }

    /**
     * @expectedException Zend\Ldap\Exception\ExceptionInterface
     */
    public function testAttachToInvalidLDAP()
    {
        $data = array(
            'dn'          => 'ou=name,dc=example,dc=org',
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node = Ldap\Node::fromArray($data);
        $this->assertFalse($node->isAttached());
        $node->attachLDAP($this->getLDAP());
    }

    public function testAttachToValidLDAP()
    {
        $data = array(
            'dn'          => $this->createDn('ou=name,'),
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node = Ldap\Node::fromArray($data);
        $this->assertFalse($node->isAttached());
        $node->attachLDAP($this->getLDAP());
        $this->assertTrue($node->isAttached());
    }

    public function testExistsDn()
    {
        $data  = array(
            'dn'          => $this->createDn('ou=name,'),
            'ou'          => array('name'),
            'l'           => array('a', 'b', 'c'),
            'objectClass' => array('organizationalUnit', 'top'),
        );
        $node1 = Ldap\Node::fromArray($data);
        $node1->attachLDAP($this->getLDAP());
        $this->assertFalse($node1->exists());
        $dn    = $this->createDn('ou=Test1,');
        $node2 = Ldap\Node::fromLDAP($dn, $this->getLDAP());
        $this->assertTrue($node2->exists());
    }

    public function testReload()
    {
        $dn   = $this->createDn('ou=Test1,');
        $node = Ldap\Node::fromLDAP($dn, $this->getLDAP());
        $node->reload();
        $this->assertEquals($dn, $node->getDn()->toString());
        $this->assertEquals('ou=Test1', $node->getRdnString());
    }

    public function testGetNode()
    {
        $dn   = $this->createDn('ou=Test1,');
        $node = $this->getLDAP()->getNode($dn);
        $this->assertEquals($dn, $node->getDn()->toString());
        $this->assertEquals("Test1", $node->getAttribute('ou', 0));
    }

    /**
     * @expectedException Zend\Ldap\Exception\ExceptionInterface
     */
    public function testGetIllegalNode()
    {
        $dn   = $this->createDn('ou=Test99,');
        $node = $this->getLDAP()->getNode($dn);
    }

    public function testGetBaseNode()
    {
        $node = $this->getLDAP()->getBaseNode();
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $node->getDnString());

        $dn = Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Ldap\Dn::ATTR_CASEFOLD_LOWER
        );
        $this->assertEquals($dn[0]['ou'], $node->getAttribute('ou', 0));
    }

    public function testSearchSubtree()
    {
        $node  = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $items = $node->searchSubtree('(objectClass=organizationalUnit)', Ldap\Ldap::SEARCH_SCOPE_SUB,
            array(), 'ou'
        );
        $this->assertInstanceOf('Zend\Ldap\Node\Collection', $items);
        $this->assertEquals(3, $items->count());

        $i   = 0;
        $dns = array(
            $this->createDn('ou=Node,'),
            $this->createDn('ou=Test1,ou=Node,'),
            $this->createDn('ou=Test2,ou=Node,'));
        foreach ($items as $key => $node) {
            $key = Ldap\Dn::fromString($key)->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
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
        $node = $this->getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(9, $node->countSubtree('(objectClass=organizationalUnit)',
                Ldap\Ldap::SEARCH_SCOPE_SUB
            )
        );
    }

    public function testCountChildren()
    {
        $node = $this->getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(6, $node->countChildren());
        $node = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $this->assertEquals(2, $node->countChildren());
    }

    public function testSearchChildren()
    {
        $node = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $this->assertEquals(2, $node->searchChildren('(objectClass=*)', array(), 'ou')->count());
        $node = $this->getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertEquals(6, $node->searchChildren('(objectClass=*)', array(), 'ou')->count());
    }

    public function testGetParent()
    {
        $node  = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $pnode = $node->getParent();
        $this->assertEquals(Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE)
                ->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER),
            $pnode->getDnString(Ldap\Dn::ATTR_CASEFOLD_LOWER)
        );
    }

    /**
     * @expectedException Zend\Ldap\Exception\ExceptionInterface
     */
    public function testGetNonexistantParent()
    {
        $node  = $this->getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $pnode = $node->getParent();
    }

    public function testLoadFromLDAPWithDnObject()
    {
        $dn   = Ldap\Dn::fromString($this->createDn('ou=Test1,'));
        $node = Ldap\Node::fromLDAP($dn, $this->getLDAP());
        $this->assertInstanceOf('Zend\Ldap\Node', $node);
        $this->assertTrue($node->isAttached());
    }
}
