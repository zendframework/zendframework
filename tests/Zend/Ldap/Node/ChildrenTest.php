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


/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class ChildrenTest extends \ZendTest\Ldap\OnlineTestCase
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

    public function testGetChildrenOnAttachedNode()
    {
        $node=$this->_getLDAP()->getBaseNode();
        $children=$node->getChildren();
        $this->assertInstanceOf('Zend\Ldap\Node\ChildrenIterator', $children);
        $this->assertEquals(6, count($children));
        $this->assertInstanceOf('Zend\Ldap\Node', $children['ou=Node']);
    }

    public function testGetChildrenOnDetachedNode()
    {
        $node=$this->_getLDAP()->getBaseNode();
        $node->detachLDAP();
        $children=$node->getChildren();
        $this->assertInstanceOf('Zend\Ldap\Node\ChildrenIterator', $children);
        $this->assertEquals(0, count($children));

        $node->attachLDAP($this->_getLDAP());
        $node->reload();
        $children=$node->getChildren();

        $this->assertInstanceOf('Zend\Ldap\Node\ChildrenIterator', $children);
        $this->assertEquals(6, count($children));
        $this->assertInstanceOf('Zend\Ldap\Node', $children['ou=Node']);
    }

    public function testHasChildrenOnAttachedNode()
    {
        $node=$this->_getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $this->assertTrue($node->hasChildren());
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $this->assertTrue($node->hasChildren());
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Test1,'));
        $this->assertFalse($node->hasChildren());
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Test1,ou=Node,'));
        $this->assertFalse($node->hasChildren());
        $this->assertFalse($node->hasChildren());
    }

    public function testHasChildrenOnDetachedNodeWithoutPriorGetChildren()
    {
        $node=$this->_getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Test1,'));
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Test1,ou=Node,'));
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());
    }

    public function testHasChildrenOnDetachedNodeWithPriorGetChildren()
    {
        $node=$this->_getLDAP()->getNode(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $node->getChildren();
        $node->detachLDAP();
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $node->getChildren();
        $node->detachLDAP();
        $this->assertTrue($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Test1,'));
        $node->getChildren();
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Test1,ou=Node,'));
        $node->getChildren();
        $node->detachLDAP();
        $this->assertFalse($node->hasChildren());
    }

    public function testChildrenCollectionSerialization()
    {
        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));

        $children=$node->getChildren();
        $this->assertTrue($node->hasChildren());
        $this->assertEquals(2, count($children));

        $string=serialize($node);
        $node2=unserialize($string);

        $children2=$node2->getChildren();
        $this->assertTrue($node2->hasChildren());
        $this->assertEquals(2, count($children2));

        $node2->attachLDAP($this->_getLDAP());

        $children2=$node2->getChildren();
        $this->assertTrue($node2->hasChildren());
        $this->assertEquals(2, count($children2));

        $node=$this->_getLDAP()->getNode($this->_createDn('ou=Node,'));
        $this->assertTrue($node->hasChildren());
        $string=serialize($node);
        $node2=unserialize($string);
        $this->assertFalse($node2->hasChildren());
        $node2->attachLDAP($this->_getLDAP());
        $this->assertTrue($node2->hasChildren());
    }

    public function testCascadingAttachAndDetach()
    {
        $node=$this->_getLDAP()->getBaseNode();
        $baseChildren=$node->getChildren();
        $nodeChildren=$baseChildren['ou=Node']->getChildren();

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

        $node->attachLDAP($this->_getLDAP());
        $this->assertTrue($node->isAttached());
        $this->assertSame($this->_getLDAP(), $node->getLDAP());
        foreach ($baseChildren as $bc) {
            $this->assertTrue($bc->isAttached());
            $this->assertSame($this->_getLDAP(), $bc->getLDAP());
        }
        foreach ($nodeChildren as $nc) {
            $this->assertTrue($nc->isAttached());
            $this->assertSame($this->_getLDAP(), $nc->getLDAP());
        }
    }
}
