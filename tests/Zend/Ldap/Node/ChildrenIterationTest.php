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
use Zend\Ldap;

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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class ChildrenIterationTest extends \ZendTest\Ldap\OnlineTestCase
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

    public function testSimpleIteration()
    {
        $node=$this->_getLDAP()->getBaseNode();
        $children=$node->getChildren();

        $i=1;
        foreach ($children as $rdn => $n) {
            $dn=$n->getDn()->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
            $rdn=Ldap\Dn::implodeRdn($n->getRdnArray(), Ldap\Dn::ATTR_CASEFOLD_LOWER);
            if ($i==1) {
                $this->assertEquals('ou=Node', $rdn);
                $this->assertEquals($this->_createDn('ou=Node,'), $dn);
            }
            else {
                $this->assertEquals('ou=Test' . ($i-1), $rdn);
                $this->assertEquals($this->_createDn('ou=Test' . ($i-1) . ','), $dn);
            }
            $i++;
        }
        $this->assertEquals(6, $i-1);
    }

    public function testSimpleRecursiveIteration()
    {
        $node=$this->_getLDAP()->getBaseNode();
        $ri=new \RecursiveIteratorIterator($node, \RecursiveIteratorIterator::SELF_FIRST);
        $i=0;
        foreach ($ri as $rdn => $n) {
            $dn=$n->getDn()->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER);
            $rdn=Ldap\Dn::implodeRdn($n->getRdnArray(), Ldap\Dn::ATTR_CASEFOLD_LOWER);
            if ($i==0) {
                $this->assertEquals(Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE)
                    ->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER), $dn);
            }
            else if ($i==1) {
                $this->assertEquals('ou=Node', $rdn);
                $this->assertEquals($this->_createDn('ou=Node,'), $dn);
            }
            else {
                if ($i<4) {
                    $j=$i-1;
                    $base=$this->_createDn('ou=Node,');
                }
                else {
                    $j=$i-3;
                    $base=Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE)
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
        $node = $this->_getLDAP()->getBaseNode();
        $nodes = $node->searchChildren('(objectClass=*)');
        foreach ($nodes as $rdn => $n) {
            // do nothing - just iterate
        }
        $nodes->next();
    }
}
