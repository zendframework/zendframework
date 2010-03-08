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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Ldap_OnlineTestCase
 */
/**
 * @see Zend_Ldap_Node
 */

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class Zend_Ldap_Node_ChildrenTest extends Zend_Ldap_OnlineTestCase
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

    public function testGetChildrenOnAttachedNode()
    {
        $node=$this->_getLdap()->getBaseNode();
        $children=$node->getChildren();
        $this->assertType('Zend_Ldap_Node_ChildrenIterator', $children);
        $this->assertEquals(6, count($children));
        $this->assertType('Zend_Ldap_Node', $children['ou=Node']);
    }

    public function testGetChildrenOnDetachedNode()
    {
        $node=$this->_getLdap()->getBaseNode();
        $node->detachLdap();
        $children=$node->getChildren();
        $this->assertType('Zend_Ldap_Node_ChildrenIterator', $children);
        $this->assertEquals(0, count($children));

        $node->attachLdap($this->_getLdap());
        $node->reload();
        $children=$node->getChildren();

        $this->assertType('Zend_Ldap_Node_ChildrenIterator', $children);
        $this->assertEquals(6, count($children));
        $this->assertType('Zend_Ldap_Node', $children['ou=Node']);
    }

    public function testHasChildrenOnAttachedNode()
    {
        $node=$this->_getLdap()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertTrue($node->hasChildren());
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $this->assertTrue($node->hasChildren());
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Test1,'));
        $this->assertFalse($node->hasChildren());
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Test1,ou=Node,'));
        $this->assertFalse($node->hasChildren());
        $this->assertFalse($node->hasChildren());
    }

    public function testHasChildrenOnDetachedNodeWithoutPriorGetChildren()
    {
        $node=$this->_getLdap()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $node->detachLdap();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $node->detachLdap();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Test1,'));
        $node->detachLdap();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Test1,ou=Node,'));
        $node->detachLdap();
        $this->assertFalse($node->hasChildren());
    }

    public function testHasChildrenOnDetachedNodeWithPriorGetChildren()
    {
        $node=$this->_getLdap()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $node->getChildren();
        $node->detachLdap();
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $node->getChildren();
        $node->detachLdap();
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Test1,'));
        $node->getChildren();
        $node->detachLdap();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Test1,ou=Node,'));
        $node->getChildren();
        $node->detachLdap();
        $this->assertFalse($node->hasChildren());
    }

    public function testChildrenCollectionSerialization()
    {
        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));

        $children=$node->getChildren();
        $this->assertTrue($node->hasChildren());
        $this->assertEquals(2, count($children));

        $string=serialize($node);
        $node2=unserialize($string);

        $children2=$node2->getChildren();
        $this->assertTrue($node2->hasChildren());
        $this->assertEquals(2, count($children2));

        $node2->attachLdap($this->_getLdap());

        $children2=$node2->getChildren();
        $this->assertTrue($node2->hasChildren());
        $this->assertEquals(2, count($children2));

        $node=$this->_getLdap()->getNode($this->_createDn('ou=Node,'));
        $this->assertTrue($node->hasChildren());
        $string=serialize($node);
        $node2=unserialize($string);
        $this->assertFalse($node2->hasChildren());
        $node2->attachLdap($this->_getLdap());
        $this->assertTrue($node2->hasChildren());
    }

    public function testCascadingAttachAndDetach()
    {
        $node=$this->_getLdap()->getBaseNode();
        $baseChildren=$node->getChildren();
        $nodeChildren=$baseChildren['ou=Node']->getChildren();

        $this->assertTrue($node->isAttached());
        foreach ($baseChildren as $bc) {
            $this->assertTrue($bc->isAttached());
        }
        foreach ($nodeChildren as $nc) {
            $this->assertTrue($nc->isAttached());
        }

        $node->detachLdap();
        $this->assertFalse($node->isAttached());
        foreach ($baseChildren as $bc) {
            $this->assertFalse($bc->isAttached());
        }
        foreach ($nodeChildren as $nc) {
            $this->assertFalse($nc->isAttached());
        }

        $node->attachLdap($this->_getLdap());
        $this->assertTrue($node->isAttached());
        $this->assertSame($this->_getLdap(), $node->getLdap());
        foreach ($baseChildren as $bc) {
            $this->assertTrue($bc->isAttached());
            $this->assertSame($this->_getLdap(), $bc->getLdap());
        }
        foreach ($nodeChildren as $nc) {
            $this->assertTrue($nc->isAttached());
            $this->assertSame($this->_getLdap(), $nc->getLdap());
        }
    }
}
