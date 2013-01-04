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
class ExplodingTest extends \PHPUnit_Framework_TestCase
{
    public static function explodeDnOperationProvider()
    {
        $testData = array(
            array('CN=Alice Baker,CN=Users,DC=example,DC=com', true),
            array('CN=Baker\\, Alice,CN=Users,DC=example,DC=com', true),
            array('OU=Sales,DC=local', true),
            array('OU=Sales;DC=local', true),
            array('OU=Sales ,DC=local', true),
            array('OU=Sales, dC=local', true),
            array('ou=Sales , DC=local', true),
            array('OU=Sales ; dc=local', true),
            array('DC=local', true),
            array(' DC=local', true),
            array('DC= local  ', true),
            array('username', false),
            array('username@example.com', false),
            array('EXAMPLE\\username', false),
            array('CN=,Alice Baker,CN=Users,DC=example,DC=com', false),
            array('CN=Users,DC==example,DC=com', false),
            array('O=ACME', true),
            array('', false),
            array('   ', false),
            array('uid=rogasawara,ou=営業部,o=Airius', true),
            array('cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com', true),
            array('CN=Steve Kille,O=Isode Limited,C=GB', true),
            array('OU=Sales+CN=J. Smith,O=Widget Inc.,C=US', true),
            array('CN=L. Eagle,O=Sue\, Grabbit and Runn,C=GB', true),
            array('CN=Before\0DAfter,O=Test,C=GB', true),
            array('SN=Lu\C4\8Di\C4\87', true),
            array('OU=Sales+,O=Widget Inc.,C=US', false),
            array('+OU=Sales,O=Widget Inc.,C=US', false),
            array('OU=Sa+les,O=Widget Inc.,C=US', false),
        );
        return $testData;
    }

    /**
     * @dataProvider explodeDnOperationProvider
     */
    public function testExplodeDnOperation($input, $expected)
    {
        $ret = Ldap\Dn::checkDn($input);
        $this->assertTrue($ret === $expected);
    }

    public function testExplodeDnCaseFold()
    {
        $dn = 'CN=Alice Baker,cn=Users,DC=example,dc=com';
        $k  = array();
        $v  = null;
        $this->assertTrue(Ldap\Dn::checkDn($dn, $k, $v, Ldap\Dn::ATTR_CASEFOLD_NONE));
        $this->assertEquals(array('CN', 'cn', 'DC', 'dc'), $k);

        $this->assertTrue(Ldap\Dn::checkDn($dn, $k, $v, Ldap\Dn::ATTR_CASEFOLD_LOWER));
        $this->assertEquals(array('cn', 'cn', 'dc', 'dc'), $k);

        $this->assertTrue(Ldap\Dn::checkDn($dn, $k, $v, Ldap\Dn::ATTR_CASEFOLD_UPPER));
        $this->assertEquals(array('CN', 'CN', 'DC', 'DC'), $k);
    }

    public function testExplodeDn()
    {
        $dn       = 'cn=name1,cn=name2,dc=example,dc=org';
        $k        = array();
        $v        = array();
        $dnArray  = Ldap\Dn::explodeDn($dn, $k, $v);
        $expected = array(
            array("cn" => "name1"),
            array("cn" => "name2"),
            array("dc" => "example"),
            array("dc" => "org")
        );
        $ke       = array('cn', 'cn', 'dc', 'dc');
        $ve       = array('name1', 'name2', 'example', 'org');
        $this->assertEquals($expected, $dnArray);
        $this->assertEquals($ke, $k);
        $this->assertEquals($ve, $v);
    }

    public function testExplodeDnWithUtf8Characters()
    {
        $dn       = 'uid=rogasawara,ou=営業部,o=Airius';
        $k        = array();
        $v        = array();
        $dnArray  = Ldap\Dn::explodeDn($dn, $k, $v);
        $expected = array(
            array("uid" => "rogasawara"),
            array("ou" => "営業部"),
            array("o" => "Airius"),
        );
        $ke       = array('uid', 'ou', 'o');
        $ve       = array('rogasawara', '営業部', 'Airius');
        $this->assertEquals($expected, $dnArray);
        $this->assertEquals($ke, $k);
        $this->assertEquals($ve, $v);
    }

    public function testExplodeDnWithSpaces()
    {
        $dn       = 'cn=Barbara Jensen, ou=Product Development, dc=airius, dc=com';
        $k        = array();
        $v        = array();
        $dnArray  = Ldap\Dn::explodeDn($dn, $k, $v);
        $expected = array(
            array("cn" => "Barbara Jensen"),
            array("ou" => "Product Development"),
            array("dc" => "airius"),
            array("dc" => "com"),
        );
        $ke       = array('cn', 'ou', 'dc', 'dc');
        $ve       = array('Barbara Jensen', 'Product Development', 'airius', 'com');
        $this->assertEquals($expected, $dnArray);
        $this->assertEquals($ke, $k);
        $this->assertEquals($ve, $v);
    }

    public function testCoreExplodeDnWithMultiValuedRdn()
    {
        $dn = 'cn=name1+uid=user,cn=name2,dc=example,dc=org';
        $k  = array();
        $v  = array();
        $this->assertTrue(Ldap\Dn::checkDn($dn, $k, $v));
        $ke = array(array('cn', 'uid'), 'cn', 'dc', 'dc');
        $ve = array(array('name1', 'user'), 'name2', 'example', 'org');
        $this->assertEquals($ke, $k);
        $this->assertEquals($ve, $v);

        $dn = 'cn=name11+cn=name12,cn=name2,dc=example,dc=org';
        $this->assertFalse(Ldap\Dn::checkDn($dn));

        $dn = 'CN=name11+Cn=name12,cn=name2,dc=example,dc=org';
        $this->assertFalse(Ldap\Dn::checkDn($dn));
    }

    public function testExplodeDnWithMultiValuedRdn()
    {
        $dn      = 'cn=Surname\, Firstname+uid=userid,cn=name2,dc=example,dc=org';
        $k       = array();
        $v       = array();
        $dnArray = Ldap\Dn::explodeDn($dn, $k, $v);
        $ke      = array(array('cn', 'uid'), 'cn', 'dc', 'dc');
        $ve      = array(array('Surname, Firstname', 'userid'), 'name2', 'example', 'org');
        $this->assertEquals($ke, $k);
        $this->assertEquals($ve, $v);
        $expected = array(
            array("cn"  => "Surname, Firstname",
                  "uid" => "userid"),
            array("cn" => "name2"),
            array("dc" => "example"),
            array("dc" => "org")
        );
        $this->assertEquals($expected, $dnArray);
    }

    public function testExplodeDnWithMultiValuedRdn2()
    {
        $dn      = 'cn=Surname\, Firstname+uid=userid+sn=Surname,cn=name2,dc=example,dc=org';
        $k       = array();
        $v       = array();
        $dnArray = Ldap\Dn::explodeDn($dn, $k, $v);
        $ke      = array(array('cn', 'uid', 'sn'), 'cn', 'dc', 'dc');
        $ve      = array(array('Surname, Firstname', 'userid', 'Surname'), 'name2', 'example', 'org');
        $this->assertEquals($ke, $k);
        $this->assertEquals($ve, $v);
        $expected = array(
            array("cn"  => "Surname, Firstname",
                  "uid" => "userid",
                  "sn"  => "Surname"),
            array("cn" => "name2"),
            array("dc" => "example"),
            array("dc" => "org")
        );
        $this->assertEquals($expected, $dnArray);
    }

    /**
     * @expectedException Zend\Ldap\Exception\ExceptionInterface
     */
    public function testCreateDnArrayIllegalDn()
    {
        $dn      = 'name1,cn=name2,dc=example,dc=org';
        $dnArray = Ldap\Dn::explodeDn($dn);
    }

    public static function rfc2253DnProvider()
    {
        $testData = array(
            array('CN=Steve Kille,O=Isode Limited,C=GB',
                  array(
                      array('CN' => 'Steve Kille'),
                      array('O'  => 'Isode Limited'),
                      array('C'  => 'GB')
                  )),
            array('OU=Sales+CN=J. Smith,O=Widget Inc.,C=US',
                  array(
                      array('OU' => 'Sales',
                            'CN' => 'J. Smith'),
                      array('O'  => 'Widget Inc.'),
                      array('C'  => 'US')
                  )),
            array('CN=L. Eagle,O=Sue\, Grabbit and Runn,C=GB',
                  array(
                      array('CN' => 'L. Eagle'),
                      array('O'  => 'Sue, Grabbit and Runn'),
                      array('C'  => 'GB')
                  )),
            array('CN=Before\0DAfter,O=Test,C=GB',
                  array(
                      array('CN' => "Before\rAfter"),
                      array('O'  => 'Test'),
                      array('C'  => 'GB')
                  )),
            array('SN=Lu\C4\8Di\C4\87',
                  array(
                      array('SN' => 'Lučić')
                  ))
        );
        return $testData;
    }

    /**
     * @dataProvider rfc2253DnProvider
     */
    public function testExplodeDnsProvidedByRFC2253($input, $expected)
    {
        $dnArray = Ldap\Dn::explodeDn($input);
        $this->assertEquals($expected, $dnArray);
    }
}
