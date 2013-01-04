<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap\Dn;

use Zend\Ldap;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Dn
 */
class MiscTest extends \PHPUnit_Framework_TestCase
{
    public function testIsChildOfIllegalDn1()
    {
        $dn1 = 'name1,cn=name2,dc=example,dc=org';
        $dn2 = 'dc=example,dc=org';
        $this->assertFalse(Ldap\Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfIllegalDn2()
    {
        $dn1 = 'cn=name1,cn=name2,dc=example,dc=org';
        $dn2 = 'example,dc=org';
        $this->assertFalse(Ldap\Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfIllegalBothDn()
    {
        $dn1 = 'name1,cn=name2,dc=example,dc=org';
        $dn2 = 'example,dc=org';
        $this->assertFalse(Ldap\Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOf()
    {
        $dn1 = 'cb=name1,cn=name2,dc=example,dc=org';
        $dn2 = 'dc=example,dc=org';
        $this->assertTrue(Ldap\Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfWithDnObjects()
    {
        $dn1 = Ldap\Dn::fromString('cb=name1,cn=name2,dc=example,dc=org');
        $dn2 = Ldap\Dn::fromString('dc=example,dc=org');
        $this->assertTrue(Ldap\Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfOtherSubtree()
    {
        $dn1 = 'cb=name1,cn=name2,dc=example,dc=org';
        $dn2 = 'dc=example,dc=de';
        $this->assertFalse(Ldap\Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfParentDnLonger()
    {
        $dn1 = 'dc=example,dc=de';
        $dn2 = 'cb=name1,cn=name2,dc=example,dc=org';
        $this->assertFalse(Ldap\Dn::isChildOf($dn1, $dn2));
    }
}
