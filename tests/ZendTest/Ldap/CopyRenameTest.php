<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap;

use Zend\Ldap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 */
class CopyRenameTest extends AbstractOnlineTestCase
{
    /**
     * @var string
     */
    private $orgDn;
    /**
     * @var string
     */
    private $newDn;
    /**
     * @var string
     */
    private $orgSubTreeDn;
    /**
     * @var string
     */
    private $newSubTreeDn;
    /**
     * @var string
     */
    private $targetSubTreeDn;

    /**
     * @var array
     */
    private $nodes;

    protected function setUp()
    {
        parent::setUp();
        $this->prepareLDAPServer();

        $this->orgDn           = $this->createDn('ou=OrgTest,');
        $this->newDn           = $this->createDn('ou=NewTest,');
        $this->orgSubTreeDn    = $this->createDn('ou=OrgSubtree,');
        $this->newSubTreeDn    = $this->createDn('ou=NewSubtree,');
        $this->targetSubTreeDn = $this->createDn('ou=Target,');

        $this->nodes = array(
            $this->orgDn        => array("objectClass" => "organizationalUnit",
                                         "ou"          => "OrgTest"),
            $this->orgSubTreeDn => array("objectClass" => "organizationalUnit",
                                         "ou"          => "OrgSubtree"),
            'ou=Subtree1,' . $this->orgSubTreeDn              =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Subtree1"),
            'ou=Subtree11,ou=Subtree1,' . $this->orgSubTreeDn =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Subtree11"),
            'ou=Subtree12,ou=Subtree1,' . $this->orgSubTreeDn =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Subtree12"),
            'ou=Subtree13,ou=Subtree1,' . $this->orgSubTreeDn =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Subtree13"),
            'ou=Subtree2,' . $this->orgSubTreeDn              =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Subtree2"),
            'ou=Subtree3,' . $this->orgSubTreeDn              =>
            array("objectClass" => "organizationalUnit",
                  "ou"          => "Subtree3"),
            $this->targetSubTreeDn => array("objectClass" => "organizationalUnit",
                                            "ou"          => "Target")
        );

        $ldap = $this->getLDAP()->getResource();
        foreach ($this->nodes as $dn => $entry) {
            ldap_add($ldap, $dn, $entry);
        }
    }

    protected function tearDown()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            return;
        }
        if ($this->getLDAP()->exists($this->newDn)) {
            $this->getLDAP()->delete($this->newDn, false);
        }
        if ($this->getLDAP()->exists($this->orgDn)) {
            $this->getLDAP()->delete($this->orgDn, false);
        }
        if ($this->getLDAP()->exists($this->orgSubTreeDn)) {
            $this->getLDAP()->delete($this->orgSubTreeDn, true);
        }
        if ($this->getLDAP()->exists($this->newSubTreeDn)) {
            $this->getLDAP()->delete($this->newSubTreeDn, true);
        }
        if ($this->getLDAP()->exists($this->targetSubTreeDn)) {
            $this->getLDAP()->delete($this->targetSubTreeDn, true);
        }


        $this->cleanupLDAPServer();
        parent::tearDown();
    }

    public function testSimpleLeafRename()
    {
        $org = $this->getLDAP()->getEntry($this->orgDn, array(), true);
        $this->getLDAP()->rename($this->orgDn, $this->newDn, false);
        $this->assertFalse($this->getLDAP()->exists($this->orgDn));
        $this->assertTrue($this->getLDAP()->exists($this->newDn));
        $new = $this->getLDAP()->getEntry($this->newDn);
        $this->assertEquals($org['objectclass'], $new['objectclass']);
        $this->assertEquals(array('NewTest'), $new['ou']);
    }

    public function testSimpleLeafMoveAlias()
    {
        $this->getLDAP()->move($this->orgDn, $this->newDn, false);
        $this->assertFalse($this->getLDAP()->exists($this->orgDn));
        $this->assertTrue($this->getLDAP()->exists($this->newDn));
    }

    public function testSimpleLeafMoveToSubtree()
    {
        $this->getLDAP()->moveToSubtree($this->orgDn, $this->orgSubTreeDn, false);
        $this->assertFalse($this->getLDAP()->exists($this->orgDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgTest,' . $this->orgSubTreeDn));
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testRenameSourceNotExists()
    {
        $this->getLDAP()->rename($this->createDn('ou=DoesNotExist,'), $this->newDn, false);
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testRenameTargetExists()
    {
        $this->getLDAP()->rename($this->orgDn, $this->createDn('ou=Test1,'), false);
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testRenameTargetParentNotExists()
    {
        $this->getLDAP()->rename($this->orgDn, $this->createDn('ou=Test1,ou=ParentDoesNotExist,'), false);
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testRenameEmulationSourceNotExists()
    {
        $this->getLDAP()->rename($this->createDn('ou=DoesNotExist,'), $this->newDn, false, true);
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testRenameEmulationTargetExists()
    {
        $this->getLDAP()->rename($this->orgDn, $this->createDn('ou=Test1,'), false, true);
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testRenameEmulationTargetParentNotExists()
    {
        $this->getLDAP()->rename($this->orgDn, $this->createDn('ou=Test1,ou=ParentDoesNotExist,'),
            false, true
        );
    }

    public function testSimpleLeafRenameEmulation()
    {
        $this->getLDAP()->rename($this->orgDn, $this->newDn, false, true);
        $this->assertFalse($this->getLDAP()->exists($this->orgDn));
        $this->assertTrue($this->getLDAP()->exists($this->newDn));
    }

    public function testSimpleLeafCopyToSubtree()
    {
        $this->getLDAP()->copyToSubtree($this->orgDn, $this->orgSubTreeDn, false);
        $this->assertTrue($this->getLDAP()->exists($this->orgDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgTest,' . $this->orgSubTreeDn));
    }

    public function testSimpleLeafCopy()
    {
        $this->getLDAP()->copy($this->orgDn, $this->newDn, false);
        $this->assertTrue($this->getLDAP()->exists($this->orgDn));
        $this->assertTrue($this->getLDAP()->exists($this->newDn));
    }

    public function testRecursiveRename()
    {
        $this->getLDAP()->rename($this->orgSubTreeDn, $this->newSubTreeDn, true);
        $this->assertFalse($this->getLDAP()->exists($this->orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists($this->newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren($this->newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $this->newSubTreeDn));
    }

    public function testRecursiveMoveToSubtree()
    {
        $this->getLDAP()->moveToSubtree($this->orgSubTreeDn, $this->targetSubTreeDn, true);
        $this->assertFalse($this->getLDAP()->exists($this->orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgSubtree,' . $this->targetSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=OrgSubtree,' . $this->targetSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $this->targetSubTreeDn));
    }

    public function testRecursiveCopyToSubtree()
    {
        $this->getLDAP()->copyToSubtree($this->orgSubTreeDn, $this->targetSubTreeDn, true);
        $this->assertTrue($this->getLDAP()->exists($this->orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgSubtree,' . $this->targetSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren($this->orgSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $this->orgSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=OrgSubtree,' . $this->targetSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $this->targetSubTreeDn));
    }

    public function testRecursiveCopy()
    {
        $this->getLDAP()->copy($this->orgSubTreeDn, $this->newSubTreeDn, true);
        $this->assertTrue($this->getLDAP()->exists($this->orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists($this->newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren($this->orgSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $this->orgSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren($this->newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $this->newSubTreeDn));
    }

    public function testSimpleLeafRenameWithDnObjects()
    {
        $orgDn = Ldap\Dn::fromString($this->orgDn);
        $newDn = Ldap\Dn::fromString($this->newDn);

        $this->getLDAP()->rename($orgDn, $newDn, false);
        $this->assertFalse($this->getLDAP()->exists($orgDn));
        $this->assertTrue($this->getLDAP()->exists($newDn));

        $this->getLDAP()->move($newDn, $orgDn, false);
        $this->assertTrue($this->getLDAP()->exists($orgDn));
        $this->assertFalse($this->getLDAP()->exists($newDn));
    }

    public function testSimpleLeafMoveToSubtreeWithDnObjects()
    {
        $orgDn        = Ldap\Dn::fromString($this->orgDn);
        $orgSubTreeDn = Ldap\Dn::fromString($this->orgSubTreeDn);

        $this->getLDAP()->moveToSubtree($orgDn, $orgSubTreeDn, false);
        $this->assertFalse($this->getLDAP()->exists($orgDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgTest,' . $orgSubTreeDn->toString()));
    }

    public function testSimpleLeafRenameEmulationWithDnObjects()
    {
        $orgDn = Ldap\Dn::fromString($this->orgDn);
        $newDn = Ldap\Dn::fromString($this->newDn);

        $this->getLDAP()->rename($orgDn, $newDn, false, true);
        $this->assertFalse($this->getLDAP()->exists($orgDn));
        $this->assertTrue($this->getLDAP()->exists($newDn));
    }

    public function testSimpleLeafCopyToSubtreeWithDnObjects()
    {
        $orgDn        = Ldap\Dn::fromString($this->orgDn);
        $orgSubTreeDn = Ldap\Dn::fromString($this->orgSubTreeDn);

        $this->getLDAP()->copyToSubtree($orgDn, $orgSubTreeDn, false);
        $this->assertTrue($this->getLDAP()->exists($orgDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgTest,' . $orgSubTreeDn->toString()));
    }

    public function testSimpleLeafCopyWithDnObjects()
    {
        $orgDn = Ldap\Dn::fromString($this->orgDn);
        $newDn = Ldap\Dn::fromString($this->newDn);

        $this->getLDAP()->copy($orgDn, $newDn, false);
        $this->assertTrue($this->getLDAP()->exists($orgDn));
        $this->assertTrue($this->getLDAP()->exists($newDn));
    }

    public function testRecursiveRenameWithDnObjects()
    {
        $orgSubTreeDn = Ldap\Dn::fromString($this->orgSubTreeDn);
        $newSubTreeDn = Ldap\Dn::fromString($this->newSubTreeDn);

        $this->getLDAP()->rename($orgSubTreeDn, $newSubTreeDn, true);
        $this->assertFalse($this->getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists($newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren($newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $newSubTreeDn->toString()));
    }

    public function testRecursiveMoveToSubtreeWithDnObjects()
    {
        $orgSubTreeDn    = Ldap\Dn::fromString($this->orgSubTreeDn);
        $targetSubTreeDn = Ldap\Dn::fromString($this->targetSubTreeDn);

        $this->getLDAP()->moveToSubtree($orgSubTreeDn, $targetSubTreeDn, true);
        $this->assertFalse($this->getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3,
            $this->getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $targetSubTreeDn->toString())
        );
    }

    public function testRecursiveCopyToSubtreeWithDnObjects()
    {
        $orgSubTreeDn    = Ldap\Dn::fromString($this->orgSubTreeDn);
        $targetSubTreeDn = Ldap\Dn::fromString($this->targetSubTreeDn);

        $this->getLDAP()->copyToSubtree($orgSubTreeDn, $targetSubTreeDn, true);
        $this->assertTrue($this->getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3, $this->getLDAP()->countChildren($orgSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $orgSubTreeDn->toString()));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=OrgSubtree,' . $targetSubTreeDn->toString()));
        $this->assertEquals(3,
            $this->getLDAP()->countChildren('ou=Subtree1,ou=OrgSubtree,' . $targetSubTreeDn->toString())
        );
    }

    public function testRecursiveCopyWithDnObjects()
    {
        $orgSubTreeDn = Ldap\Dn::fromString($this->orgSubTreeDn);
        $newSubTreeDn = Ldap\Dn::fromString($this->newSubTreeDn);

        $this->getLDAP()->copy($orgSubTreeDn, $newSubTreeDn, true);
        $this->assertTrue($this->getLDAP()->exists($orgSubTreeDn));
        $this->assertTrue($this->getLDAP()->exists($newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren($orgSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $orgSubTreeDn->toString()));
        $this->assertEquals(3, $this->getLDAP()->countChildren($newSubTreeDn));
        $this->assertEquals(3, $this->getLDAP()->countChildren('ou=Subtree1,' . $newSubTreeDn->toString()));
    }
}
