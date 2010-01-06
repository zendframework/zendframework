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
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
/**
 * Zend_Ldap_Filter
 */
require_once 'Zend/Ldap/Filter.php';
/**
 * Zend_Ldap_Filter_And
 */
require_once 'Zend/Ldap/Filter/And.php';
/**
 * Zend_Ldap_Filter_Or
 */
require_once 'Zend/Ldap/Filter/Or.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_FilterTest extends PHPUnit_Framework_TestCase
{
    public function testFilterEscapeBasicOperation()
    {
        $input = 'a*b(b)d\e/f';
        $expected = 'a\2ab\28b\29d\5ce/f';
        $this->assertEquals($expected, Zend_Ldap_Filter::escapeValue($input));
    }

    public function testEscapeValues()
    {
        $expected='t\28e,s\29t\2av\5cal\1eue';
        $filterval='t(e,s)t*v\\al' . chr(30) . 'ue';
        $this->assertEquals($expected, Zend_Ldap_Filter::escapeValue($filterval));
        $this->assertEquals($expected, Zend_Ldap_Filter::escapeValue(array($filterval)));
        $this->assertEquals(array($expected, $expected, $expected),
            Zend_Ldap_Filter::escapeValue(array($filterval, $filterval, $filterval)));
    }

    public function testUnescapeValues()
    {
        $expected='t(e,s)t*v\\al' . chr(30) . 'ue';
        $filterval='t\28e,s\29t\2av\5cal\1eue';
        $this->assertEquals($expected, Zend_Ldap_Filter::unescapeValue($filterval));
        $this->assertEquals($expected, Zend_Ldap_Filter::unescapeValue(array($filterval)));
        $this->assertEquals(array($expected, $expected, $expected),
            Zend_Ldap_Filter::unescapeValue(array($filterval, $filterval, $filterval)));
    }

    public function testFilterValueUtf8()
    {
        $filter='ÄÖÜäöüß€';
        $escaped=Zend_Ldap_Filter::escapeValue($filter);
        $unescaped=Zend_Ldap_Filter::unescapeValue($escaped);
        $this->assertEquals($filter, $unescaped);
    }

    public function testFilterCreation()
    {
        $f1=Zend_Ldap_Filter::equals('name', 'value');
        $this->assertEquals('(name=value)', $f1->toString());
        $f2=Zend_Ldap_Filter::begins('name', 'value');
        $this->assertEquals('(name=value*)', $f2->toString());
        $f3=Zend_Ldap_Filter::ends('name', 'value');
        $this->assertEquals('(name=*value)', $f3->toString());
        $f4=Zend_Ldap_Filter::contains('name', 'value');
        $this->assertEquals('(name=*value*)', $f4->toString());
        $f5=Zend_Ldap_Filter::greater('name', 'value');
        $this->assertEquals('(name>value)', $f5->toString());
        $f6=Zend_Ldap_Filter::greaterOrEqual('name', 'value');
        $this->assertEquals('(name>=value)', $f6->toString());
        $f7=Zend_Ldap_Filter::less('name', 'value');
        $this->assertEquals('(name<value)', $f7->toString());
        $f8=Zend_Ldap_Filter::lessOrEqual('name', 'value');
        $this->assertEquals('(name<=value)', $f8->toString());
        $f9=Zend_Ldap_Filter::approx('name', 'value');
        $this->assertEquals('(name~=value)', $f9->toString());
        $f10=Zend_Ldap_Filter::any('name');
        $this->assertEquals('(name=*)', $f10->toString());
        $f11=Zend_Ldap_Filter::string('name=*value*value*');
        $this->assertEquals('(name=*value*value*)', $f11->toString());
        $f12=Zend_Ldap_Filter::mask('(&(objectClass=account)(uid=%s))', 'a*b(b)d\e/f');
        $this->assertEquals('(&(objectClass=account)(uid=a\2ab\28b\29d\5ce/f))', $f12->toString());
    }

    public function testToStringImplementation()
    {
        $f1=Zend_Ldap_Filter::ends('name', 'value');
        $this->assertEquals($f1->toString(), (string)$f1);
    }

    public function testNegate()
    {
        $f1=Zend_Ldap_Filter::ends('name', 'value');
        $this->assertEquals('(name=*value)', $f1->toString());
        $f1=$f1->negate();
        $this->assertEquals('(!(name=*value))', $f1->toString());
        $f1=$f1->negate();
        $this->assertEquals('(name=*value)', $f1->toString());
    }

    /**
     * @expectedException Zend_Ldap_Filter_Exception
     */
    public function testIllegalGroupingFilter()
    {
        $data=array('a', 'b', 5);
        $f=new Zend_Ldap_Filter_And($data);
    }

    public function testGroupingFilter()
    {
        $f1=Zend_Ldap_Filter::equals('name', 'value');
        $f2=Zend_Ldap_Filter::begins('name', 'value');
        $f3=Zend_Ldap_Filter::ends('name', 'value');

        $f4=Zend_Ldap_Filter::andFilter($f1, $f2, $f3);
        $f5=Zend_Ldap_Filter::orFilter($f1, $f2, $f3);

        $this->assertEquals('(&(name=value)(name=value*)(name=*value))', $f4->toString());
        $this->assertEquals('(|(name=value)(name=value*)(name=*value))', $f5->toString());

        $f4=$f4->addFilter($f1);
        $this->assertEquals('(&(name=value)(name=value*)(name=*value)(name=value))', $f4->toString());
    }

    public function testComplexFilter()
    {
        $f1=Zend_Ldap_Filter::equals('name1', 'value1');
        $f2=Zend_Ldap_Filter::equals('name1', 'value2');

        $f3=Zend_Ldap_Filter::equals('name2', 'value1');
        $f4=Zend_Ldap_Filter::equals('name2', 'value2');

        $f5=Zend_Ldap_Filter::orFilter($f1, $f2);
        $f6=Zend_Ldap_Filter::orFilter($f3, $f4);

        $f7=Zend_Ldap_Filter::andFilter($f5, $f6);

        $this->assertEquals('(&(|(name1=value1)(name1=value2))(|(name2=value1)(name2=value2)))',
            $f7->toString());
    }

    public function testChaining()
    {
        $f=Zend_Ldap_Filter::equals('a1', 'v1')
            ->addAnd(Zend_Ldap_Filter::approx('a2', 'v2'));
        $this->assertEquals('(&(a1=v1)(a2~=v2))', $f->toString());
        $f=Zend_Ldap_Filter::equals('a1', 'v1')
            ->addOr(Zend_Ldap_Filter::approx('a2', 'v2'));
        $this->assertEquals('(|(a1=v1)(a2~=v2))', $f->toString());
        $f=Zend_Ldap_Filter::equals('a1', 'v1')
            ->negate()
            ->addOr(Zend_Ldap_Filter::approx('a2', 'v2'));
        $this->assertEquals('(|(!(a1=v1))(a2~=v2))', $f->toString());
        $f=Zend_Ldap_Filter::equals('a1', 'v1')
            ->addAnd(Zend_Ldap_Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(a1=v1)(!(a2~=v2)))', $f->toString());
        $f=Zend_Ldap_Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Zend_Ldap_Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(!(a1=v1))(!(a2~=v2)))', $f->toString());
        $f=Zend_Ldap_Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Zend_Ldap_Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(!(a1=v1))(!(a2~=v2)))', $f->toString());
        $f=Zend_Ldap_Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Zend_Ldap_Filter::approx('a2', 'v2')->negate())
            ->negate();
        $this->assertEquals('(!(&(!(a1=v1))(!(a2~=v2))))', $f->toString());
    }

    public function testRealFilterString()
    {
        $f1=Zend_Ldap_Filter::orFilter(
            Zend_Ldap_Filter::equals('sn', 'Gehrig'),
            Zend_Ldap_Filter::equals('sn', 'Goerke')
        );
        $f2=Zend_Ldap_Filter::orFilter(
            Zend_Ldap_Filter::equals('givenName', 'Stefan'),
            Zend_Ldap_Filter::equals('givenName', 'Ingo')
        );

        $f=Zend_Ldap_Filter::andFilter($f1, $f2);

        $this->assertEquals('(&(|(sn=Gehrig)(sn=Goerke))(|(givenName=Stefan)(givenName=Ingo)))',
            $f->toString());
    }
}

