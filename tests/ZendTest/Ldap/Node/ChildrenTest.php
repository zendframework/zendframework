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

use ZendTest\Ldap as TestLdap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class ChildrenTest extends TestLdap\AbstractOnlineTestCase
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

    public function testGetChildrenOnAttachedNode()
    {
        $node     = $this->getLDAP()->getBaseNode();
        $children = $node->getChildren();
        $this->assertInstanceOf('Zend\Ldap\Node\ChildrenIterator', $children);
        $this->assertEquals(6, count($children));
        $this->assertInstanceOf('Zend\Ldap\Node', $children['ou=Node']);
    }

    public function testGetChildrenOnDetachedNode()
    {
        $node = $this->getLDAP()->getBaseNode();
        $node->detachLDAP();
        $children = $node->getChildren();
        $this->assertInstanceOf('Zend\Ldap\Node\ChildrenIterator', $children);
        $this->assertEquals(0, count($children));

        $node->attachLDAP($this->getLDAP());
        $node->reload();
        $children = $node->getChildren();

        $this->assertInstanceOf('Zend\Ldap\Node\ChildrenIterator', $children);
        $this->assertEquals(6, count($children));
        $this->assertInstanceOf('Zend\Ldap\Node', $children['ou=Node']);
    }

    public function testHasChildrenOnAttachedNode()
    {
        $node = $this->getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertTrue($node->hasChildren());
        $this->assertTrue($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $this->assertTrue($node->hasChildren());
        $this->assertTrue($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Test1,'));
        $this->assertFalse($node->hasChildren());
        $this->assertFalse($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Test1,ou=Node,'));
        $this->assertFalse($node->hasChildren());
        $this->assertFalse($node->hasChildren());
    }

    public function testHasChildrenOnDetachedNodeWithoutPriorGetChildren()
    {
        $node = $this->getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Test1,'));
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Test1,ou=Node,'));
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());
    }

    public function testHasChildrenOnDetachedNodeWithPriorGetChildren()
    {
        $node = $this->getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $node->getChildren();
        $node->detachLDAP();
        $this->assertTrue($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $node->getChildren();
        $node->detachLDAP();
        $this->assertTrue($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Test1,'));
        $node->getChildren();
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node = $this->getLDAP()->getNode($this->createDn('ou=Test1,ou=Node,'));
        $node->getChildren();
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());
    }

    public function testChildrenCollectionSerialization()
    {
        $node = $this->getLDAP()->getNode($this->createDn('ou=Node,'));

        $children = $node->getChildren();
        $this->assertTrue($node->hasChildren());
        $this->assertEquals(2, count($children));

        $string = serialize($node);
        $node2  = unserialize($string);

        $children2 = $node2->getChildren();
        $this->assertTrue($node2->hasChildren());
        $this->assertEquals(2, count($children2));

        $node2->attachLDAP($this->getLDAP());

        $children2 = $node2->getChildren();
        $this->assertTrue($node2->hasChildren());
        $this->assertEquals(2, count($children2));

        $node = $this->getLDAP()->getNode($this->createDn('ou=Node,'));
        $this->assertTrue($node->hasChildren());
        $string = serialize($node);
        $node2  = unserialize($string);
        $this->assertFalse($node2->hasChildren());
        $node2->attachLDAP($this->getLDAP());
        $this->assertTrue($node2->hasChildren());
    }

    public function testCascadingAttachAndDetach()
    {
        $node         = $this->getLDAP()->getBaseNode();
        $baseChildren = $node->getChildren();
        $nodeChildren = $baseChildren['ou=Node']->getChildren();

        $this->assertTrue($node->isAttached());
        foreach ($baseChildren as $bc) {
            $this->assertTrue($bc->isAttached());
        }
        foreach ($nodeChildren as $nc) {
            $this->assertTrue($nc->isAttached());
        }

        $node->detachLDAP();
        $this->assertFalse($node->isAttached());
        foreach ($baseChildren as $bc) {
            $this->assertFalse($bc->isAttached());
        }
        foreach ($nodeChildren as $nc) {
            $this->assertFalse($nc->isAttached());
        }

        $node->attachLDAP($this->getLDAP());
        $this->assertTrue($node->isAttached());
        $this->assertSame($this->getLDAP(), $node->getLDAP());
        foreach ($baseChildren as $bc) {
            $this->assertTrue($bc->isAttached());
            $this->assertSame($this->getLDAP(), $bc->getLDAP());
        }
        foreach ($nodeChildren as $nc) {
            $this->assertTrue($nc->isAttached());
            $this->assertSame($this->getLDAP(), $nc->getLDAP());
        }
    }
}
