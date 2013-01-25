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
use Zend\Ldap\Exception;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Dn
 */
class CreationTest extends \PHPUnit_Framework_TestCase
{
    public function testDnCreation()
    {
        Ldap\Dn::setDefaultCaseFold(Ldap\Dn::ATTR_CASEFOLD_NONE);

        $dnString1 = 'CN=Baker\\, Alice,CN=Users+OU=Lab,DC=example,DC=com';
        $dnArray1  = array(
            array('CN' => 'Baker, Alice'),
            array('CN' => 'Users',
                  'OU' => 'Lab'),
            array('DC' => 'example'),
            array('DC' => 'com'));

        $dnString2 = 'cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example,dc=com';
        $dnArray2  = array(
            array('cn' => 'Baker, Alice'),
            array('cn' => 'Users',
                  'ou' => 'Lab'),
            array('dc' => 'example'),
            array('dc' => 'com'));

        $dnString3 = 'Cn=Baker\\, Alice,Cn=Users+Ou=Lab,Dc=example,Dc=com';
        $dnArray3  = array(
            array('Cn' => 'Baker, Alice'),
            array('Cn' => 'Users',
                  'Ou' => 'Lab'),
            array('Dc' => 'example'),
            array('Dc' => 'com'));

        $dn11 = Ldap\Dn::fromString($dnString1);
        $dn12 = Ldap\Dn::fromArray($dnArray1);
        $dn13 = Ldap\Dn::factory($dnString1);
        $dn14 = Ldap\Dn::factory($dnArray1);

        $this->assertEquals($dn11, $dn12);
        $this->assertEquals($dn11, $dn13);
        $this->assertEquals($dn11, $dn14);

        $this->assertEquals($dnString1, $dn11->toString());
        $this->assertEquals($dnString1, $dn11->toString(Ldap\Dn::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnString2, $dn11->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray1, $dn11->toArray());
        $this->assertEquals($dnArray1, $dn11->toArray(Ldap\Dn::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnArray2, $dn11->toArray(Ldap\Dn::ATTR_CASEFOLD_LOWER));

        $dn21 = Ldap\Dn::fromString($dnString2);
        $dn22 = Ldap\Dn::fromArray($dnArray2);
        $dn23 = Ldap\Dn::factory($dnString2);
        $dn24 = Ldap\Dn::factory($dnArray2);

        $this->assertEquals($dn21, $dn22);
        $this->assertEquals($dn21, $dn23);
        $this->assertEquals($dn21, $dn24);

        $this->assertEquals($dnString2, $dn21->toString());
        $this->assertEquals($dnString1, $dn21->toString(Ldap\Dn::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnString2, $dn21->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray2, $dn21->toArray());
        $this->assertEquals($dnArray1, $dn21->toArray(Ldap\Dn::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnArray2, $dn21->toArray(Ldap\Dn::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray2, $dn22->toArray());

        $dn31 = Ldap\Dn::fromString($dnString3);
        $dn32 = Ldap\Dn::fromArray($dnArray3);
        $dn33 = Ldap\Dn::factory($dnString3);
        $dn34 = Ldap\Dn::factory($dnArray3);

        $this->assertEquals($dn31, $dn32);
        $this->assertEquals($dn31, $dn33);
        $this->assertEquals($dn31, $dn34);

        $this->assertEquals($dnString3, $dn31->toString());
        $this->assertEquals($dnString1, $dn31->toString(Ldap\Dn::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnString2, $dn31->toString(Ldap\Dn::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray3, $dn31->toArray());
        $this->assertEquals($dnArray1, $dn31->toArray(Ldap\Dn::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnArray2, $dn31->toArray(Ldap\Dn::ATTR_CASEFOLD_LOWER));

        try {
            Ldap\Dn::factory(1);
            $this->fail('Expected Zend\Ldap\Exception not thrown');
        } catch (Exception\LdapException $e) {
            $this->assertEquals('Invalid argument type for $dn', $e->getMessage());
        }
    }

    public function testDnCreationWithDifferentCaseFoldings()
    {
        Ldap\Dn::setDefaultCaseFold(Ldap\Dn::ATTR_CASEFOLD_NONE);

        $dnString1 = 'Cn=Baker\\, Alice,Cn=Users+Ou=Lab,Dc=example,Dc=com';
        $dnString2 = 'CN=Baker\\, Alice,CN=Users+OU=Lab,DC=example,DC=com';
        $dnString3 = 'cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example,dc=com';

        $dn = Ldap\Dn::fromString($dnString1, null);
        $this->assertEquals($dnString1, (string)$dn);
        $dn->setCaseFold(Ldap\Dn::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);
        $dn->setCaseFold(Ldap\Dn::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);

        $dn = Ldap\Dn::fromString($dnString1, Ldap\Dn::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);
        $dn->setCaseFold(null);
        $this->assertEquals($dnString1, (string)$dn);
        $dn->setCaseFold(Ldap\Dn::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);

        $dn = Ldap\Dn::fromString($dnString1, Ldap\Dn::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);
        $dn->setCaseFold(Ldap\Dn::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);
        $dn->setCaseFold(Ldap\Dn::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);
        $dn->setCaseFold(Ldap\Dn::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);

        Ldap\Dn::setDefaultCaseFold(Ldap\Dn::ATTR_CASEFOLD_UPPER);
        $dn = Ldap\Dn::fromString($dnString1, null);
        $this->assertEquals($dnString2, (string)$dn);

        Ldap\Dn::setDefaultCaseFold(null);
        $dn = Ldap\Dn::fromString($dnString1, null);
        $this->assertEquals($dnString1, (string)$dn);

        Ldap\Dn::setDefaultCaseFold(Ldap\Dn::ATTR_CASEFOLD_NONE);
    }

    public function testGetRdn()
    {
        Ldap\Dn::setDefaultCaseFold(Ldap\Dn::ATTR_CASEFOLD_NONE);

        $dnString = 'cn=Baker\\, Alice,cn=Users,dc=example,dc=com';
        $dn       = Ldap\Dn::fromString($dnString);

        $this->assertEquals(array('cn' => 'Baker, Alice'), $dn->getRdn());
        $this->assertEquals('cn=Baker\\, Alice', $dn->getRdnString());

        $dnString = 'Cn=Users+Ou=Lab,dc=example,dc=com';
        $dn       = Ldap\Dn::fromString($dnString);
        $this->assertEquals(array('Cn' => 'Users',
                                 'Ou'  => 'Lab'), $dn->getRdn()
        );
        $this->assertEquals('Cn=Users+Ou=Lab', $dn->getRdnString());
    }

    public function testGetParentDn()
    {
        $dnString = 'cn=Baker\\, Alice,cn=Users,dc=example,dc=com';
        $dn       = Ldap\Dn::fromString($dnString);

        $this->assertEquals('cn=Users,dc=example,dc=com', $dn->getParentDn()->toString());
        $this->assertEquals('cn=Users,dc=example,dc=com', $dn->getParentDn(1)->toString());
        $this->assertEquals('dc=example,dc=com', $dn->getParentDn(2)->toString());
        $this->assertEquals('dc=com', $dn->getParentDn(3)->toString());

        try {
            $dn->getParentDn(0)->toString();
            $this->fail('Expected Zend\Ldap\Exception not thrown');
        } catch (Exception\LdapException $e) {
            $this->assertEquals('Cannot retrieve parent DN with given $levelUp', $e->getMessage());
        }
        try {
            $dn->getParentDn(4)->toString();
            $this->fail('Expected Zend\Ldap\Exception not thrown');
        } catch (Exception\LdapException $e) {
            $this->assertEquals('Cannot retrieve parent DN with given $levelUp', $e->getMessage());
        }
    }

    public function testEmptyStringDn()
    {
        $dnString = '';
        $dn       = Ldap\Dn::fromString($dnString);

        $this->assertEquals($dnString, $dn->toString());
    }
}
