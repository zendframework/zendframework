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
namespace ZendTest\Ldap;
use Zend\Ldap;


/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class CopyRenameTest extends OnlineTestCase
{
    /**
     * @var string
     */
    private $_orgDn;
    /**
     * @var string
     */
    private $_newDn;
    /**
     * @var string
     */
    private $_orgSubTreeDn;
    /**
     * @var string
     */
    private $_newSubTreeDn;
    /**
     * @var string
     */
    private $_targetSubTreeDn;

    /**
     * @var array
     */
    private $_nodes;

    protected function setUp()
    {
        parent::setUp();
        $this->_prepareLDAPServer();

        $this->_orgDn=$this->_createDn('ou=OrgTest,');
        $this->_newDn=$this->_createDn('ou=NewTest,');
        $this->_orgSubTreeDn=$this->_createDn('ou=OrgSubtree,');
        $this->_newSubTreeDn=$this->_createDn('ou=NewSubtree,');
        $this->_targetSubTreeDn=$this->_createDn('ou=Target,');

        $this->_nodes=array(
            $this->_orgDn => array("objectClass" => "organizationalUnit", "ou" => "OrgTest"),
            $this->_orgSubTreeDn =>  array("objectClass" => "organizationalUnit", "ou" => "OrgSubtree"),
            'ou=Subtree1,' . $this->_orgSubTreeDn =>
                array("objectClass" => "organizationalUnit", "ou" => "Subtree1"),
            'ou=Subtree11,ou=Subtree1,' . $this->_orgSubTreeDn =>
                array("objectClass" => "organizationalUnit", "ou" => "Subtree11"),
            'ou=Subtree12,ou=Subtree1,' . $this->_orgSubTreeDn =>
                array("objectClass" => "organizationalUnit", "ou" => "Subtree12"),
            'ou=Subtree13,ou=Subtree1,' . $this->_orgSubTreeDn =>
                array("objectClass" => "organizationalUnit", "ou" => "Subtree13"),
            'ou=Subtree2,' . $this->_orgSubTreeDn =>
                array("objectClass" => "organizationalUnit", "ou" => "Subtree2"),
            'ou=Subtree3,' . $this->_orgSubTreeDn =>
                array("objectClass" => "organizationalUnit", "ou" => "Subtree3"),
            $this->_targetSubTreeDn => array("objectClass" => "organizationalUnit", "ou" => "Target")
        );

        $ldap=$this->_getLDAP()->getResource();
        foreach ($this->_nodes as $dn => $entry) {
            ldap_add($ldap, $dn, $entry);
        }
    }

    protected function tearDown()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            return;
        }
        if ($this->_getLDAP()->exists($this->_newDn))
            $this->_getLDAP()->delete($this->_newDn, false);
        if ($this->_getLDAP()->exists($this->_orgDn))
            $this->_getLDAP()->delete($this->_orgDn, false);
        if ($this->_getLDAP()->exists($this->_orgSubTreeDn))
            $this->_getLDAP()->delete($this->_orgSubTreeDn, true);
        if ($this->_getLDAP()->exists($this->_newSubTreeDn))
            $this->_getLDAP()->delete($this->_newSubTreeDn, true);
        if ($this->_getLDAP()->exists($this->_targetSubTreeDn))
            $this->_getLDAP()->delete($this->_targetSubTreeDn, true);


        $this->_cleanupLDAPServer();
        parent::tearDown();
    }

    public function testSimpleLeafRename()
    {
        $org=$this->_getLDAP()->getEntry($this->_orgDn, array(), true);
        $this->_getLDAP()->rename($this->_orgDn, $this->_newDn, false);
        $this->assertFalse($this->_getLDAP()->exists($this->_orgDn));
        $this->assertTrue($this->_getLDAP()->exists($this->_newDn));
        $new=$this->_getLDAP()->getEntry($this->_newDn);
        $this->assertEquals($org['objectclass'], $new['objectclass']);
        $this->assertEquals(array('NewTest'), $new['ou']);
    }

    public function testSimpleLeafMoveAlias()
    {
        $this->_getLDAP()->move($this->_orgDn, $this->_newDn, false);
        $this->assertFalse($this->_getLDAP()->exists($this->_orgDn));
        $this->assertTrue($this->_getLDAP()->exists($this->_newDn));
    }

    public function testSimpleLeafMoveToSubtree()
    {
        $this->_getLDAP()->moveToSubtree($this->_orgDn, $this->_orgSubTreeDn, false);
        $this->assertFalse($this->_getLDAP()->exists($this->_orgDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgTest,' . $this->_orgSubTreeDn));
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testRenameSourceNotExists()
    {
        $this->_getLDAP()->rename($this->_createDn('ou=DoesNotExist,'), $this->_newDn, false);
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testRenameTargetExists()
    {
        $this->_getLDAP()->rename($this->_orgDn, $this->_createDn('ou=Test1,'), false);
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testRenameTargetParentNotExists()
    {
        $this->_getLDAP()->rename($this->_orgDn, $this->_createDn('ou=Test1,ou=ParentDoesNotExist,'), false);
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testRenameEmulationSourceNotExists()
    {
        $this->_getLDAP()->rename($this->_createDn('ou=DoesNotExist,'), $this->_newDn, false, true);
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testRenameEmulationTargetExists()
    {
        $this->_getLDAP()->rename($this->_orgDn, $this->_createDn('ou=Test1,'), false, true);
    }

    /**
     * @expectedException Zend\Ldap\Exception
     */
    public function testRenameEmulationTargetParentNotExists()
    {
        $this->_getLDAP()->rename($this->_orgDn, $this->_createDn('ou=Test1,ou=ParentDoesNotExist,'),
            false, true);
    }

    public function testSimpleLeafRenameEmulation()
    {
        $this->_getLDAP()->rename($this->_orgDn, $this->_newDn, false, true);
        $this->assertFalse($this->_getLDAP()->exists($this->_orgDn));
        $this->assertTrue($this->_getLDAP()->exists($this->_newDn));
    }

    public function testSimpleLeafCopyToSubtree()
    {
        $this->_getLDAP()->copyToSubtree($this->_orgDn, $this->_orgSubTreeDn, false);
        $this->assertTrue($this->_getLDAP()->exists($this->_orgDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgTest,' . $this->_orgSubTreeDn));
    }

    public function testSimpleLeafCopy()
    {
        $this->_getLDAP()->copy($this->_orgDn, $this->_newDn, false);
        $this->assertTrue($this->_getLDAP()->exists($this->_orgDn));
        $this->assertTrue($this->_getLDAP()->exists($this->_newDn));
    }

    public function testRecursiveRename()
    {
        $this->_getLDAP()->rename($this->_orgSubTreeDn, $this->_newSubTreeDn, true);
        $this->assertFalse($this->_getLDAP()->exists($this->_orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists($this->_newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($this->_newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $this->_newSubTreeDn));
    }

    public function testRecursiveMoveToSubtree()
    {
        $this->_getLDAP()->moveToSubtree($this->_orgSubTreeDn, $this->_targetSubTreeDn, true);
        $this->assertFalse($this->_getLDAP()->exists($this->_orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgSubtree,' . $this->_targetSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=OrgSubtree,' . $this->_targetSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $this->_targetSubTreeDn));
    }

    public function testRecursiveCopyToSubtree()
    {
        $this->_getLDAP()->copyToSubtree($this->_orgSubTreeDn, $this->_targetSubTreeDn, true);
        $this->assertTrue($this->_getLDAP()->exists($this->_orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgSubtree,' . $this->_targetSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($this->_orgSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $this->_orgSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=OrgSubtree,' . $this->_targetSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $this->_targetSubTreeDn));
    }

    public function testRecursiveCopy()
    {
        $this->_getLDAP()->copy($this->_orgSubTreeDn, $this->_newSubTreeDn, true);
        $this->assertTrue($this->_getLDAP()->exists($this->_orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists($this->_newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($this->_orgSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $this->_orgSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($this->_newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $this->_newSubTreeDn));
    }

    public function testSimpleLeafRenameWithDnObjects()
    {
        $orgDn=Ldap\Dn::fromString($this->_orgDn);
        $newDn=Ldap\Dn::fromString($this->_newDn);

        $this->_getLDAP()->rename($orgDn, $newDn, false);
        $this->assertFalse($this->_getLDAP()->exists($orgDn));
        $this->assertTrue($this->_getLDAP()->exists($newDn));

        $this->_getLDAP()->move($newDn, $orgDn, false);
        $this->assertTrue($this->_getLDAP()->exists($orgDn));
        $this->assertFalse($this->_getLDAP()->exists($newDn));
    }

    public function testSimpleLeafMoveToSubtreeWithDnObjects()
    {
        $orgDn=Ldap\Dn::fromString($this->_orgDn);
        $orgSubTreeDn=Ldap\Dn::fromString($this->_orgSubTreeDn);

        $this->_getLDAP()->moveToSubtree($orgDn, $orgSubTreeDn, false);
        $this->assertFalse($this->_getLDAP()->exists($orgDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgTest,' . $orgSubTreeDn->toString()));
    }

    public function testSimpleLeafRenameEmulationWithDnObjects()
    {
        $orgDn=Ldap\Dn::fromString($this->_orgDn);
        $newDn=Ldap\Dn::fromString($this->_newDn);

        $this->_getLDAP()->rename($orgDn, $newDn, false, true);
        $this->assertFalse($this->_getLDAP()->exists($orgDn));
        $this->assertTrue($this->_getLDAP()->exists($newDn));
    }

    public function testSimpleLeafCopyToSubtreeWithDnObjects()
    {
        $orgDn=Ldap\Dn::fromString($this->_orgDn);
        $orgSubTreeDn=Ldap\Dn::fromString($this->_orgSubTreeDn);

        $this->_getLDAP()->copyToSubtree($orgDn, $orgSubTreeDn, false);
        $this->assertTrue($this->_getLDAP()->exists($orgDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgTest,' . $orgSubTreeDn->toString()));
    }

    public function testSimpleLeafCopyWithDnObjects()
    {
        $orgDn=Ldap\Dn::fromString($this->_orgDn);
        $newDn=Ldap\Dn::fromString($this->_newDn);

        $this->_getLDAP()->copy($orgDn, $newDn, false);
        $this->assertTrue($this->_getLDAP()->exists($orgDn));
        $this->assertTrue($this->_getLDAP()->exists($newDn));
    }

    public function testRecursiveRenameWithDnObjects()
    {
        $orgSubTreeDn=Ldap\Dn::fromString($this->_orgSubTreeDn);
        $newSubTreeDn=Ldap\Dn::fromString($this->_newSubTreeDn);

        $this->_getLDAP()->rename($orgSubTreeDn, $newSubTreeDn, true);
        $this->assertFalse($this->_getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists($newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $newSubTreeDn->toString()));
    }

    public function testRecursiveMoveToSubtreeWithDnObjects()
    {
        $orgSubTreeDn=Ldap\Dn::fromString($this->_orgSubTreeDn);
        $targetSubTreeDn=Ldap\Dn::fromString($this->_targetSubTreeDn);

        $this->_getLDAP()->moveToSubtree($orgSubTreeDn, $targetSubTreeDn, true);
        $this->assertFalse($this->_getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $targetSubTreeDn->toString()));
    }

    public function testRecursiveCopyToSubtreeWithDnObjects()
    {
        $orgSubTreeDn=Ldap\Dn::fromString($this->_orgSubTreeDn);
        $targetSubTreeDn=Ldap\Dn::fromString($this->_targetSubTreeDn);

        $this->_getLDAP()->copyToSubtree($orgSubTreeDn, $targetSubTreeDn, true);
        $this->assertTrue($this->_getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($orgSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $orgSubTreeDn->toString()));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $targetSubTreeDn->toString()));
    }

    public function testRecursiveCopyWithDnObjects()
    {
        $orgSubTreeDn=Ldap\Dn::fromString($this->_orgSubTreeDn);
        $newSubTreeDn=Ldap\Dn::fromString($this->_newSubTreeDn);

        $this->_getLDAP()->copy($orgSubTreeDn, $newSubTreeDn, true);
        $this->assertTrue($this->_getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->_getLDAP()->exists($newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($orgSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $orgSubTreeDn->toString()));
        $this->assertEquals(3, $this->_getLDAP()->countChildren($newSubTreeDn));
        $this->assertEquals(3, $this->_getLDAP()->countChildren('ou=Subtree1,' . $newSubTreeDn->toString()));
    }
}
