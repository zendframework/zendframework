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
use ZendTest\Ldap as TestLdap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class ChildrenIterationTest extends TestLdap\AbstractOnlineTestCase
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

    public function testSimpleIteration()
    {
        $node     = $this->getLDAP()->getBaseNode();
        $children = $node->getChildren();

        $i = 1;
        foreach ($children as $rdn => $n) {
            $dn  = $n->getDn()->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
            $rdn = Ldap\Dn::implodeRdn($n->getRdnArray(), Ldap\Dn::ATTR_CASEFOLD_LOWER);
            if ($i == 1) {
                $this->assertEquals('ou=Node', $rdn);
                $this->assertEquals($this->createDn('ou=Node,'), $dn);
            } else {
                $this->assertEquals('ou=Test' . ($i - 1), $rdn);
                $this->assertEquals($this->createDn('ou=Test' . ($i - 1) . ','), $dn);
            }
            $i++;
        }
        $this->assertEquals(6, $i - 1);
    }

    public function testSimpleRecursiveIteration()
    {
        $node = $this->getLDAP()->getBaseNode();
        $ri   = new \RecursiveIteratorIterator($node, \RecursiveIteratorIterator::SELF_FIRST);
        $i    = 0;
        foreach ($ri as $rdn => $n) {
            $dn  = $n->getDn()->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
            $rdn = Ldap\Dn::implodeRdn($n->getRdnArray(), Ldap\Dn::ATTR_CASEFOLD_LOWER);
            if ($i == 0) {
                $this->assertEquals(Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE)
                        ->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER), $dn
                );
            } elseif ($i == 1) {
                $this->assertEquals('ou=Node', $rdn);
                $this->assertEquals($this->createDn('ou=Node,'), $dn);
            } else {
                if ($i < 4) {
                    $j    = $i - 1;
                    $base = $this->createDn('ou=Node,');
                } else {
                    $j    = $i - 3;
                    $base = Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE)
                        ->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
                }
                $this->assertEquals('ou=Test' . $j, $rdn);
                $this->assertEquals('ou=Test' . $j . ',' . $base, $dn);
            }
            $i++;
        }
        $this->assertEquals(9, $i);
    }

    /**
     * Test issue reported by Lance Hendrix on
     * http://framework.zend.com/wiki/display/ZFPROP/Zend_Ldap+-+Extended+support+-+Stefan+Gehrig?
     *      focusedCommentId=13107431#comment-13107431
     */
    public function testCallingNextAfterIterationShouldNotThrowException()
    {
        $node  = $this->getLDAP()->getBaseNode();
        $nodes = $node->searchChildren('(objectClass=*)');
        foreach ($nodes as $rdn => $n) {
            // do nothing - just iterate
        }
        $nodes->next();
    }
}
